<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class UserRejection extends Notification implements ShouldQueue
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
        if($this->userData['status']==1)
        {
            // return (new MailMessage)
            //         ->subject("User Registration Update Request")
            //         ->line('Perhatian! Adalah dimaklumkan bahawa akaun anda kini tidak aktif di dalam sistem NPIS. Sila kemaskini profil melalui pautan dibawah.')
            //         // ->line($this->userData['comment'])
            //         ->action('Kemaskini butiran pendaftaran', $this->userData['Url'])
            //         ->line('Terima kasih.');

            return (new MailMessage)
                    ->subject("KEMASKINI AKAUN NATIONAL PROJECT INFORMATION SYSTEM")
                    ->view('email.user_registration_update', [
                                                    'user' =>  $this->userData
                                                 ]);
        }
        else
        {
            return (new MailMessage)
                    ->subject("User Registration Rejection")
                    ->line('Dukacita dimaklumkan bahawa permohonan anda TIDAK BERJAYA didaftarkan di dalam sistem NPIS.Sila hubungi pentadbir sistem di npispentadbir@water.gov.my')
                    // ->line($this->userData['comment'])
                    ->line('Terima kasih.');
        }
        
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
