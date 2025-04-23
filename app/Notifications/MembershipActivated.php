<?php

// app/Notifications/MembershipActivated.php
namespace App\Notifications;

use App\Models\MembershipTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipActivated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @param MembershipTransaction $transaction
     * @return void
     */
    public function __construct(MembershipTransaction $transaction)
    {
        $this->transaction = $transaction;
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
        $reseller = $this->transaction->reseller;
        $package = $this->transaction->package;
        
        return (new MailMessage)
            ->subject('Membership Anda Telah Diaktifkan')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Membership Anda telah berhasil diaktifkan.')
            ->line('Detail Membership:')
            ->line('Paket: ' . $package->name)
            ->line('Level: ' . ucfirst($package->level))
            ->line('Aktif hingga: ' . $reseller->membership_expires_at->format('d M Y H:i'))
            ->line('Status: Aktif')
            ->action('Lihat Membership', route('reseller.membership.index'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}