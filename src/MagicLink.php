<?php

namespace NorbyBaru\Passwordless;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use UnexpectedValueException;

/**
 * Class Passwordless
 */
class MagicLink
{
    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'passwordless.invalid_token';

    /**
     * Constant representing a throttled reset attempt.
     *
     * @var string
     */
    const TOKEN_THROTTLED = 'passwordless.throttled';

    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const MAGIC_LINK_SENT = 'passwordless.sent';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'passwordless.invalid_user';

    /**
     * @var string
     */
    const MAGIC_LINK_VERIFIED = 'passwordless.verified';

    protected TokenInterface $token;

    protected UserProvider $user;

    public function __construct(TokenInterface $tokenInterface, UserProvider $user)
    {
        $this->token = $tokenInterface;
        $this->user = $user;
    }

    public function generateUrl(CanUsePasswordlessAuthenticatable $notifiable, string $token): string
    {
        return URL::temporarySignedRoute(
            'passwordless.login',
            Carbon::now()->addSeconds(config('passwordless.magic_link_timeout')),
            [
                'email' => $notifiable->getEmailForMagicLink(),
                'hash' => sha1($notifiable->getEmailForMagicLink()),
                'token' => $token,
            ],
        );
    }

    public function sendLink(array $credentials): string
    {
        $user = $this->findUser($credentials);

        if (! $user) {
            return static::INVALID_USER;
        }

        if (! $token = $this->createToken($user)) {
            return static::TOKEN_THROTTLED;
        }

        $user->sendAuthenticationMagicLink($token);

        return static::MAGIC_LINK_SENT;
    }

    public function validateMagicLink(array $credentials): string|CanUsePasswordlessAuthenticatable|Authenticatable
    {
        $user = $this->findUser($credentials);

        if (! $user) {
            return static::INVALID_USER;
        }

        if (! $this->isValidToken($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        $this->clearUserTokens($user);

        return $user;
    }

    public function isValidToken(CanUsePasswordlessAuthenticatable $user, string $token): bool
    {
        if ($this->token->exist($user, $token)) {
            return true;
        }

        return false;
    }

    public function createToken(CanUsePasswordlessAuthenticatable $user): ?string
    {
        return $this->token->create($user);
    }

    private function findUser(array $credentials): bool|CanUsePasswordlessAuthenticatable|Authenticatable
    {
        $credentials = Arr::except($credentials, ['token', 'hash']);
        $user = $this->user->retrieveByCredentials($credentials);

        if (! $user) {
            return false;
        }

        if (! $user instanceof CanUsePasswordlessAuthenticatable) {
            throw new UnexpectedValueException('User must implement CanUsePasswordlessAuthentication interface.');
        }

        return $user;
    }

    private function clearUserTokens(CanUsePasswordlessAuthenticatable $user): bool
    {
        return $this->token->delete($user);
    }
}
