<?php namespace NorbyBaru\Passwordless;

use http\Exception\UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;

/**
 * Class Passwordless
 * @package NorbyBaru\Passwordless
 */
class MagicLink
{
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

    public function sendLink(array $credentials)
    {
        $user = $this->findUser($credentials);

        if (!$user) {

        }

        $token = $this->createToken($user);

        return $token;
    }


    public function validateMagicLink(array $credentials)
    {
        $user = $this->findUser($credentials);

        if (!$user) {
            return false;
        }

        if (!$this->validateToken($user, $credentials['token'])) {

        }

        return $user;
    }

    public function validateToken(CanUsePasswordlessAuthentication $user, string $token)
    {
        if ($this->token->exist($user, $token)) {
            return false;
        }

        return true;
    }

    /**
     * Generate Token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     * @return string
     */
    public function createToken(CanUsePasswordlessAuthentication $user): string
    {
        return $this->token->create($user);
    }


    /**
     * Find user by credentials supplied
     *
     * @param array $credentials
     * @return bool|CanUsePasswordlessAuthentication|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private function findUser(array $credentials)
    {
        $credentials = Arr::except($credentials, 'token');
        $user = $this->user->retrieveByCredentials($credentials);

        if (!$user) {
            return false;
        }

        if ($user && !$user instanceof CanUsePasswordlessAuthentication) {
            throw new UnexpectedValueException("User must implement CanUsePasswordlessAuthentication interface.");
        }

        return $user;
    }
}
