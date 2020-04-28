<?php namespace NorbyBaru\Passwordless;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;
use UnexpectedValueException;

/**
 * Class Passwordless
 * @package NorbyBaru\Passwordless
 */
class MagicLink
{
    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'passwordless.token';

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
    const INVALID_USER = 'passwordless.user';

    /**
     *
     * @var string
     */
    const MAGIC_LINK_VERIFIED = 'passwordless.verified';


    /** @var \NorbyBaru\Passwordless\TokenInterface  */
    protected $token;

    /** @var \Illuminate\Contracts\Auth\UserProvider  */
    protected $user;

    /**
     * Passwordless constructor.
     *
     * @param \NorbyBaru\Passwordless\TokenInterface  $tokenInterface
     * @param \Illuminate\Contracts\Auth\UserProvider $user
     */
    public function __construct(TokenInterface $tokenInterface, UserProvider $user)
    {
        $this->token = $tokenInterface;
        $this->user = $user;
    }

    /**
     * @param array $credentials
     *
     * @return bool|string
     */
    public function sendLink(array $credentials)
    {
        $user = $this->findUser($credentials);

        if (!$user) {
            return static::INVALID_USER;
        }

        if (!$token = $this->createToken($user)) {
            return static::TOKEN_THROTTLED;
        }

        $user->sendAuthenticationMagicLink($token);

        return static::MAGIC_LINK_SENT;
    }

    /**
     * @param array $credentials
     *
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|\NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable|null
     */
    public function validateMagicLink(array $credentials)
    {
        $user = $this->findUser($credentials);

        if (!$user) {
            return static::INVALID_USER;
        }

        if (!$this->isValidToken($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        $this->clearUserTokens($user);

        return $user;
    }

    /**
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     * @param string                                                    $token
     *
     * @return bool
     */
    public function isValidToken(CanUsePasswordlessAuthenticatable $user, string $token)
    {
        if ($this->token->exist($user, $token)) {
            return true;
        }

        return false;
    }

    /**
     * Generate Token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     *
     * @return string
     */
    public function createToken(CanUsePasswordlessAuthenticatable $user)
    {
        return $this->token->create($user);
    }


    /**
     * Find user by credentials supplied
     *
     * @param array $credentials
     *
     * @return bool|CanUsePasswordlessAuthenticatable|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private function findUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token', 'hash']);
        $user = $this->user->retrieveByCredentials($credentials);

        if (!$user) {
            return false;
        }

        if ($user && !$user instanceof CanUsePasswordlessAuthenticatable) {
            throw new UnexpectedValueException("User must implement CanUsePasswordlessAuthentication interface.");
        }

        return $user;
    }

    /**
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable $user
     *
     * @return bool
     */
    private function clearUserTokens(CanUsePasswordlessAuthenticatable $user)
    {
        return $this->token->delete($user);
    }
}
