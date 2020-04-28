<?php namespace NorbyBaru\Passwordless\Traits;

use NorbyBaru\Passwordless\Notifications\SendMagicLinkNotification;

/**
 * Class PasswordlessAuthenticatable
 * @package NorbyBaru\Passwordless\Traits
 */
trait PasswordlessAuthenticatable
{

    /**
     * Get Email address to send magic link
     *
     * @return string
     */
    public function getEmailForMagicLink()
    {
        return $this->email;
    }

    /**
     * Send Magic link to user to login.
     *
     * @param $token
     * @return void
     */
    public function sendAuthenticationMagicLink($token)
    {
        $this->notify(new SendMagicLinkNotification($token));
    }
}
