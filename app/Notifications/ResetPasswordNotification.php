<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Bangun email reset password dalam Bahasa Indonesia dengan tautan ke halaman aplikasi.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi SmartFood Home')
            ->line('Kami menerima permintaan penggantian kata sandi untuk akun SmartFood Home Anda.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line("Tautan ini akan kedaluwarsa dalam {$expire} menit.")
            ->line('Jika Anda tidak meminta penggantian kata sandi, abaikan email ini.');
    }
}
