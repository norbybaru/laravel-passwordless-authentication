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
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     *
     * @return string
     */
    public function create(CanUsePasswordlessAuthentication $user);

    /**
     * Token exists and valid
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     * @param string                                                   $token
     *
     * @return bool
     */
    public function exist(CanUsePasswordlessAuthentication $user, string $token);

    /**
     * Deleted existing token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     *
     * @return bool
     */
    public function delete(CanUsePasswordlessAuthentication $user);

    /**
     * Delete all expired token
     * @return bool
     */
    public function deleteExpired();
}
