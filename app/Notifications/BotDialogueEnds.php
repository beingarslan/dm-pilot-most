<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BotDialogueEnds extends Notification implements ShouldQueue
{
    use Queueable;

    public $transcript;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transcript = [])
    {
        $this->transcript = $transcript;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $users  = array_unique(array_column($this->transcript, 'sender'));
        $user_1 = $users[0];
        $user_2 = $users[1];

        return (new MailMessage)
            ->subject(__('Dialogue transcript between :user_1 and :user_2 at (:date)', [
                'user_1' => $user_1,
                'user_2' => $user_2,
                'date'   => now()->format('j/n'),
            ]))
            ->view('emails.dialogue_ends', [
                'notifiable' => $notifiable,
                'transcript' => $this->transcript,
                'user_1'     => $user_1,
                'user_2'     => $user_2,
            ]);
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
            //
        ];
    }
}
