<?php

namespace App\Listeners;

use App\Events\FileEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSpace
{
    /**
     * Comprueba si el evento es de creación o de borrado.
     *
     * @param FileEvent $event
     * @return boolean
     */
    private function isCreateEvent($event) {
        return $event instanceof \App\Events\FileCreated;
    }

    /**
     * Suma el espacio dado al espacio usado por la cuenta de un usuario. 
     *
     * @param \App\Account $account
     * @param int $space
     * @return void
     */
    private function updateSpaceUsed($account, $space) {
        $account->space += $space;
        $account->save();
    }

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
     * @param  FileCreated  $event
     * @return void
     */
    public function handle(FileEvent $event)
    {
        $file = $event->file;

        $space = $this->isCreateEvent($event)
            ? $file->size
            : $file->size * (-1);
        
        if($file->account()->exists()) {
            $account = $file->account;

            $this->updateSpaceUsed($account, $space);
        }
        else if($file->group()->exists()) {
            $accounts = $file->group->accounts;
            $spacePerAccount = ($space / $accounts->count());

            foreach($accounts as $account) {
                $this->updateSpaceUsed($account, $spacePerAccount);
            }
        }
    }
}
