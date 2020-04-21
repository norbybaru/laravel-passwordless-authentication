<?php namespace NorbyBaru\Passwordless\Traits;


use NorbyBaru\Passwordless\Notifications\SendMagicLinkNotification;

/**
 * Class CanUsePasswordlessAuthentication
 * @package NorbyBaru\Passwordless\Traits
 */
trait CanUsePasswordlessAuthentication
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
