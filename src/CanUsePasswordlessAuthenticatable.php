<?php namespace NorbyBaru\Passwordless;

/**
 * Interface CanUsePasswordlessAuthenticatable
 * @package NorbyBaru\Passwordless
 */
interface CanUsePasswordlessAuthenticatable
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
