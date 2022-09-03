<?php

namespace NorbyBaru\Passwordless\Traits;

use NorbyBaru\Passwordless\Notifications\SendMagicLinkNotification;

trait PasswordlessAuthenticatable
{
    protected ?string $magicLinkToken;

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
        $this->setGeneratedMagicLinkToken($token);
        $this->notify(new SendMagicLinkNotification($token));
    }

    public function getGeneratedMagicLinkToken(): ?string
    {
        return $this->magicLinkToken;
    }

    public function setGeneratedMagicLinkToken(string $token): void
    {
        $this->magicLinkToken = $token;
    }
}
