<?php

namespace App\Notifications;

use App\File;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FileChange extends Notification
{
    use Queueable;

    /**
     * Fichero que ha recibido cambios.
     *
     * @var \App\File
     */
    public $file;

    /**
     * Acción realizada sobre el fichero.
     *
     * @var string
     */
    public $action;

    /**
     * Create a new notification instance.
     *
     * @param File $file
     * @param string $action
     * @return void
     */
    public function __construct(File $file, $action)
    {
        $this->file = $file;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'file' => $this->file->toArray(),
            'action' => $this->action,
        ];
    }
}
