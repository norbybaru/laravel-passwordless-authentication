<?php

namespace NorbyBaru\Passwordless\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use NorbyBaru\Passwordless\Facades\Passwordless;

class SendMagicLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $token)
    {
    }

    /**
     * Get the notification's channels.
     */
    public function via($notifiable): array|string
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Sign in to :app_name', ['app_name' => env('APP_NAME', 'Laravel')]))
            ->line(Lang::get('Click the link below to sign in to your account.'))
            ->line(Lang::get('This link will expire in :count minutes and can only be used once.', ['count' => config('passwordless.magic_link_timeout')]))
            ->action(Lang::get('Sign In to :app_name', ['app_name' => env('APP_NAME', 'Laravel')]), $this->verificationUrl($notifiable))
            ->line(Lang::get('If you did not make this request, no further action is required.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return Passwordless::magicLink()->generateUrl($notifiable, $this->token);
    }
}
