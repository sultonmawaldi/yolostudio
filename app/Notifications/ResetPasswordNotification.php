<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Reset Kata Sandi - Yolo Studio')
            ->greeting('Halo!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun kamu.')
            ->line('Klik tombol di bawah ini untuk membuat password baru.')
            ->action('Reset Kata Sandi', url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line('Link ini hanya berlaku selama 60 menit.')
            ->line('Jika kamu tidak merasa meminta reset password, abaikan email ini.')
            ->salutation('Salam hangat,  
Tim Yolo Studio');
    }
}
