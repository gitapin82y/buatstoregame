<?php

// app/Notifications/TransactionPaid.php
namespace App\Notifications;

use App\Models\UserTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionPaid extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @param UserTransaction $transaction
     * @return void
     */
    public function __construct(UserTransaction $transaction)
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
        $game = $this->transaction->game;
        $service = $this->transaction->service;
        $option = $this->transaction->option;
        $reseller = $this->transaction->reseller;
        
        return (new MailMessage)
            ->subject('Pembayaran Berhasil')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Pembayaran Anda telah berhasil.')
            ->line('Detail Transaksi:')
            ->line('Invoice: ' . $this->transaction->invoice_number)
            ->line('Produk: ' . $game->name . ' - ' . $service->name . ' - ' . $option->name)
            ->line('Jumlah: Rp ' . number_format($this->transaction->amount, 0, ',', '.'))
            ->line('Status: Diproses')
            ->line('Pesanan Anda sedang diproses dan akan segera diselesaikan.')
            ->action('Lacak Pesanan', route('user.transactions.show', $this->transaction->id))
            ->line('Terima kasih telah berbelanja di ' . $reseller->store_name . '!');
    }
}