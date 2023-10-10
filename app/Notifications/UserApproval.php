<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class UserApproval extends Notification implements ShouldQueue
{
    use Queueable;

    private $password;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $password,\App\Models\User $user)
    {
        //
        $this->password = $password;
        $this->user = $user;
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
        //     ->greeting('Hello!')
        //     // ->from('barrett@example.com', 'Barrett Blair')
        //     // ->subject('Notification Subject')
        //     ->line('Adalah dimaklumkan bahawa anda telah berjaya didaftarkan di dalam sistem NPIS. Sila rujuk maklumat dibawah:')
        //     ->line('Gelaran Pengguna : ')
        //     ->line('Nama Pengguna : ' .$this->user->name)
        //     ->line('Organisasi Pengguna: ' )
        //     ->line('Tarikh Kuatkuasa Pengguna: ' )
        //     ->line('Tarikh Luput Pengguna: ' )
        //     ->line('ID Pengguna: ' .$this->user->no_ic)
        //     ->line('Kata Laluan: ' .$this->password)
        //     ->line('Mohon kemaskini kataluan anda selepas log masuk ini.')
        //     ->line('Sistem boleh dicapai di alamat https://npis.water.gov.my')
        //     ->line('Terima Kasih');

            return (new MailMessage)
                    ->subject("PENGAKTIFAN AKAUN NATIONAL PROJECT INFORMATION SYSTEM")
                    ->view('email.user_approve', [
                                                    'user' =>  $this->user,
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
