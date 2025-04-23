<?php


// app/Notifications/WithdrawalApproved.php
namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalApproved extends Notification implements ShouldQueue
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
            ->subject('Penarikan Dana Disetujui')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Permintaan penarikan dana Anda telah disetujui dan telah ditransfer ke rekening Anda.')
            ->line('Detail Penarikan:')
            ->line('Jumlah: Rp ' . number_format($this->withdrawal->amount, 0, ',', '.'))
            ->line('Bank: ' . $this->withdrawal->bank_name)
            ->line('Nomor Rekening: ' . $this->withdrawal->account_number)
            ->line('Nama Pemilik: ' . $this->withdrawal->account_name)
            ->line('Status: Disetujui')
            ->line('Tanggal Persetujuan: ' . $this->withdrawal->approved_at->format('d M Y H:i'))
            ->action('Lihat Bukti Transfer', route('reseller.withdrawals.index'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}