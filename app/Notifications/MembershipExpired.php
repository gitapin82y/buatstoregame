<?php

// app/Notifications/MembershipExpired.php
namespace App\Notifications;

use App\Models\ResellerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpired extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reseller;

    /**
     * Create a new notification instance.
     *
     * @param ResellerProfile $reseller
     * @return void
     */
    public function __construct(ResellerProfile $reseller)
    {
        $this->reseller = $reseller;
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
            ->subject('Membership Anda Telah Berakhir')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Membership Anda telah berakhir pada ' . $this->reseller->membership_expires_at->format('d M Y H:i') . '.')
            ->line('Toko Anda masih dapat diakses selama 7 hari (masa tenggang), namun tidak dapat digunakan untuk transaksi.')
            ->line('Setelah masa tenggang, toko Anda akan dinonaktifkan.')
            ->action('Perpanjang Sekarang', route('reseller.membership.index'))
            ->line('Segera perpanjang membership Anda untuk mengaktifkan kembali toko Anda.')
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}