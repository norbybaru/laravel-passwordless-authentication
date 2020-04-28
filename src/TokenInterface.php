<?php namespace NorbyBaru\Passwordless;

use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Interface TokenInterface
 * @package NorbyBaru\Passwordless
 */
interface TokenInterface
{
    /**
     * Create new Token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     *
     * @return string
     */
    public function create(CanUsePasswordlessAuthenticatable $user);

    /**
     * Token exists and valid
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     * @param string                                                    $token
     *
     * @return bool
     */
    public function exist(CanUsePasswordlessAuthenticatable $user, string $token);

    /**
     * Deleted existing token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     *
     * @return bool
     */
    public function delete(CanUsePasswordlessAuthenticatable $user);

    /**
     * Delete all expired token
     * @return bool
     */
    public function deleteExpired();
}
