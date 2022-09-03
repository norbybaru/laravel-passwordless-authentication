<?php

namespace NorbyBaru\Passwordless;

interface TokenInterface
{
    /**
     * Create new Token
     */
    public function create(CanUsePasswordlessAuthenticatable $user): ?string;

    /**
     * Token exists and valid
     */
    public function exist(CanUsePasswordlessAuthenticatable $user, string $token): bool;

    /**
     * Deleted existing token
     */
    public function delete(CanUsePasswordlessAuthenticatable $user): bool;

    /**
     * Delete all expired token
     */
    public function deleteExpired(): bool;
}
