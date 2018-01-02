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
        }
    }
}
