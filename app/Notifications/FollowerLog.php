<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class FollowerLog extends Notification
{
    protected $options;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->options = $options;
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
    public function toDatabase($notifiable)
    {
        return [
            'account'    => $this->options['account'],
            'account_id' => $this->options['account_id'],
            'action'     => $this->options['action'],
            'pk'         => $this->options['pk'],
            'username'   => $this->options['username'],
        ];
    }
}
