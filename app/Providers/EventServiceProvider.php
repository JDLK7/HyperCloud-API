<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\FileCreated' => [
            'App\Listeners\UpdateSpace',
        ],
        'App\Events\FileDeleted' => [
            'App\Listeners\UpdateSpace',
        ],
        'App\Events\SuscriptionDeleted' => [
            'App\Listeners\UpdateSuscription',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
