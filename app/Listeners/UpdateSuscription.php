<?php

namespace App\Listeners;

use App\Account;
use App\Suscription;
use App\Events\SuscriptionDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\SuscriptionDeleted as SuscriptionDeletedNotification;

class UpdateSuscription
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SuscriptionDeleted  $event
     * @return void
     */
    public function handle(SuscriptionDeleted $event)
    {
        $accounts = Account::whereNull('suscription_id')->get();
        $suscription = Suscription::orderBy('price')->first();

        foreach ($accounts as $account) {
            $account->user->notify(new SuscriptionDeletedNotification());
            
            $account->suscription()->associate($suscription);
            $account->save();
        }
    }
}
