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
        factory(App\Account::class, 10)
            ->create()
            ->each(function ($account) {
                $account->archives()
                    ->save(factory(App\Archive::class)->create());
            });
    }
}
