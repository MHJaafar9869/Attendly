<?php

namespace Modules\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtp extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $otp,
    ) {
        // ....
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your OTP')
            ->greeting("Hello {$notifiable->last_name},")
            ->line("Your one-time password (OTP) is: **{$this->otp}**")
            ->action('Verify Now', url("api/v1/auth/{$notifiable->id}/verify-otp/{$this->otp}"))
            ->line('This OTP will expire in 10 minutes.')
            ->line('If you did not request this, please ignore this message.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
