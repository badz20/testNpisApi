<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class UserRegistrattion extends Notification implements ShouldQueue
{
    use Queueable;


    private $tempuser;
    private $password;


    /**
     * Create a new notification instance.
     *
     * @return void
     */


    public function __construct(string $password,\App\Models\tempUser $user)
    {
        //
        $this->tempuser = $user;
        $this->password = $password;
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
        // return (new MailMessage)
        //         ->greeting('Hello!')
        //         ->line('Adalah dimaklumkan bahawa anda telah berjaya didaftarkan di dalam sistem NPIS. Sila rujuk maklumat dibawah:')
        //         ->line('Gelaran Pengguna : ')
        //         ->line('Nama Pengguna : ' .$this->tempuser->name)
        //         ->line('Organisasi Pengguna: ' )
        //         ->line('Tarikh Kuatkuasa Pengguna: ' )
        //         ->line('Tarikh Luput Pengguna: ' )
        //         ->line('ID Pengguna: ' .$this->tempuser->no_ic)
        //         // ->line('Kata Laluan: ' .$this->password)
        //         ->line('Mohon kemaskini kataluan anda selepas log masuk ini.')
        //         ->line('Sistem boleh dicapai di alamat https://npis.water.gov.my')
        //         ->line('Terima Kasih');

        return (new MailMessage)
                    ->subject("PENDAFTARAN AKAUN NATIONAL PROJECT INFORMATION SYSTEM")
                    ->view('email.user_registration', [
                                                    'user' =>  $this->tempuser,
                                                    'password' => $this->password
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
