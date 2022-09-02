<?php namespace NorbyBaru\Passwordless\Notifications;



use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

/**
 * Class SendMagicLinkNotification
 * @package NorbyBaru\Passwordless\Notifications
 */
class SendMagicLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var string  */
    protected $token;

    /**
     * SendMagicLinkNotification constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
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
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
         return URL::temporarySignedRoute(
            'passwordless.login',
            Carbon::now()->addMinutes(config('passwordless.magic_link_timeout')),
            [
                'email' => $notifiable->getEmailForMagicLink(),
                'hash' => sha1($notifiable->getEmailForMagicLink()),
                'token' => $this->token,
            ]
        );
    }
}
