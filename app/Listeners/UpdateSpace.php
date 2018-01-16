<?php

namespace App\Listeners;

use App\Events\FileCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSpace
{
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
    public function handle(FileCreated $event)
    {
        $file = $event->file;
        
        if($file->account()->exists()) {
            $account = $file->account;

            $this->updateSpaceUsed($account, $file->size);
        }
        else if($file->group()->exists()) {
            $accounts = $file->group->accounts;
            $size = ($file->size / $accounts->count());

            foreach($accounts as $account) {
                $this->updateSpaceUsed($account, $size);
            }
        }
    }
}
