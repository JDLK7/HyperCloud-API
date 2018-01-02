<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suscriptions = factory(App\Suscription::class, 3)->create();

        foreach($suscriptions as $sus) {
            /**
             * Cuentas SIN grupo.
             */
            factory(App\Account::class, 10)
                ->create([
                    'suscription_id' => $sus->id
                ])
                ->each(function ($account) {
                    $account->archives()
                        ->save(factory(App\Archive::class)->create());
                    $account->folders()
                        ->save(factory(App\Folder::class)->create());
                });

            /**
             * Cuentas CON grupo.
             */
            $accounts = factory(App\Account::class, 3)
                ->create([
                    'suscription_id' => $sus->id
                ])
                ->each(function ($account) {
                    $account->archives()
                        ->save(factory(App\Archive::class)->create());
                    $account->folders()
                        ->save(factory(App\Folder::class)->create());
                });
            
            factory(App\Group::class)
                ->create()
                ->each(function ($group) use (&$accounts) {
                    $group->accounts()->attach($accounts);
                    $group->archives()
                        ->save(factory(App\Archive::class)->create());
                    $group->folders()
                        ->save(factory(App\Folder::class)->create());
                });
        }
    }
}
