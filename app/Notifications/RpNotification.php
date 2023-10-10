<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RP\RpPermohonan;

class RpNotification extends Notification
{
    use Queueable;

    private $workflow;
    private $bahagian_id;
    private $rp_permohonan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $workflow,RpPermohonan $rp_permohonan,$bahagian_id = null)
    {
        //
        $this->workflow = $workflow;
        $this->bahagian_id = $bahagian_id;
        $this->rp_permohonan = $rp_permohonan;
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
        $butirans = $this->rp_permohonan->butirans;
        $butiran = '';
        if($butirans) {
            $butiran = $butirans[0]->butiran_permohona;
        }
        if($this->bahagian_id != null) {
            return (new MailMessage)
                    ->subject('MAKLUMBALAS ' . $this->rp_permohonan->tajuk . ' TUJUAN PERCUBAAN - SILA ABAIKAN EMAIL INI')
                    ->markdown('notification.rp.index', ['rp_permohonan' => $this->rp_permohonan,'bahagian' => $this->bahagian_id,'butiran' => $butiran]);
        }else {

            $butiran = $this->rp_permohonan->butirans;
            $butiran = '';
            if($butirans) {
                $butiran = $butirans[0]->butiran_permohona;
            }
            $bahagian = \App\Models\refBahagian::whereId($this->bahagian_id)->first();
            $nama_bahagian = '';
            if($bahagian) {
                $bahagian->nama_bahagian;
            }
            return (new MailMessage)
                ->subject('MAKLUMBALAS ' . $this->rp_permohonan->tajuk . ' TUJUAN PERCUBAAN - SILA ABAIKAN EMAIL INI')
                ->markdown('notification.rp.index', ['rp_permohonan' => $this->rp_permohonan,'bahagian' => $bahagian,'butiran' => $butiran]);
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
