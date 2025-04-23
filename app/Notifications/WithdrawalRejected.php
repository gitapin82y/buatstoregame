<?php


// app/Notifications/WithdrawalRejected.php
namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $withdrawal;

    /**
     * Create a new notification instance.
     *
     * @param Withdrawal $withdrawal
     * @return void
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
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
            ->subject('Penarikan Dana Ditolak')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Mohon maaf, permintaan penarikan dana Anda ditolak.')
            ->line('Detail Penarikan:')
            ->line('Jumlah: Rp ' . number_format($this->withdrawal->amount, 0, ',', '.'))
            ->line('Bank: ' . $this->withdrawal->bank_name)
            ->line('Nomor Rekening: ' . $this->withdrawal->account_number)
            ->line('Nama Pemilik: ' . $this->withdrawal->account_name)
            ->line('Status: Ditolak')
            ->line('Alasan: ' . $this->withdrawal->rejection_reason)
            ->line('Dana telah dikembalikan ke saldo Anda.')
            ->action('Lihat Saldo', route('reseller.dashboard'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}