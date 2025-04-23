<?php
// app/Notifications/ResellerWelcome.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResellerWelcome extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
            ->subject('Selamat Datang di BuatTokoGame')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Selamat bergabung di platform BuatTokoGame sebagai Reseller.')
            ->line('Akun Anda telah berhasil dibuat oleh admin. Untuk mengakses akun Anda, silakan atur password terlebih dahulu.')
            ->action('Atur Password', url('password/reset', $this->token))
            ->line('Segera atur password Anda untuk mulai menggunakan layanan kami.')
            ->line('Terima kasih telah bergabung dengan BuatTokoGame!');
    }
}