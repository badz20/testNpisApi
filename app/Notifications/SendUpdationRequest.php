<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class SendUpdationRequest extends Notification implements ShouldQueue
{

    use Queueable;
    
    private $userData;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userData)
    {
        $this->userData = $userData;      
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
        return (new MailMessage)
                    ->subject("Project Updation Notification")
                    ->line("Salam Sejahtera,")
                    ->line("Sila semak ulasan dan kemas kini permohonan anda.")
                    ->line($this->userData['comment'])
                    ->line('Thank you');
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
