<?php


// app/Notifications/MembershipExpiring.php
namespace App\Notifications;

use App\Models\ResellerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reseller;
    protected $daysLeft;

    /**
     * Create a new notification instance.
     *
     * @param ResellerProfile $reseller
     * @param int $daysLeft
     * @return void
     */
    public function __construct(ResellerProfile $reseller, int $daysLeft)
    {
        $this->reseller = $reseller;
        $this->daysLeft = $daysLeft;
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
            ->subject('Membership Anda Akan Segera Berakhir')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Membership Anda akan berakhir dalam ' . $this->daysLeft . ' hari.')
            ->line('Detail Membership:')
            ->line('Level: ' . ucfirst($this->reseller->membership_level))
            ->line('Berakhir pada: ' . $this->reseller->membership_expires_at->format('d M Y H:i'))
            ->action('Perpanjang Sekarang', route('reseller.membership.index'))
            ->line('Segera perpanjang membership Anda untuk melanjutkan menggunakan semua fitur dan layanan kami.')
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}