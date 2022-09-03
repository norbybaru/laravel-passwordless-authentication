<?php

namespace NorbyBaru\Passwordless;

interface CanUsePasswordlessAuthenticatable
{
    /**
     * Get Email address to send magic link
     */
    public function getEmailForMagicLink(): string;

    /**
     * Send Magic link to user to login.
     */
    public function sendAuthenticationMagicLink(string $token): void;

    public function getGeneratedMagicLinkToken(): ?string;

    public function setGeneratedMagicLinkToken(string $token): void;
}
