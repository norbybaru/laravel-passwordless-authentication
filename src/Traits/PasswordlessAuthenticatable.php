<?php

namespace NorbyBaru\Passwordless\Traits;

use NorbyBaru\Passwordless\Notifications\SendMagicLinkNotification;

trait PasswordlessAuthenticatable
{
    /**
     * Get Email address to send magic link
     */
    public function getEmailForMagicLink(): string
    {
        return $this->email;
    }

    /**
     * Send Magic link to user to login.
     */
    public function sendAuthenticationMagicLink(string $token): void
    {
        $this->notify(new SendMagicLinkNotification($token));
    }
}
