<?php namespace NorbyBaru\Passwordless;


/**
 * Interface CanUsePasswordlessAuthentication
 * @package NorbyBaru\Passwordless
 */
interface CanUsePasswordlessAuthentication
{
    /**
     * Get Email address to send magic link
     *
     * @return string
     */
    public function getEmailForMagicLink();

    /**
     * Send Magic link to user to login.
     *
     * @param $token
     * @return void
     */
    public function sendAuthenticationMagicLink($token);
}
