<?php namespace NorbyBaru\Passwordless;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class TokenRepository
 * @package NorbyBaru\Passwordless
 */
class TokenRepository implements TokenInterface
{
    /** @var \Illuminate\Database\ConnectionInterface  */
    protected $databaseConnection;

    /** @var string */
    protected $table;

    /** @var string  */
    protected $hashKey;

    /** @var string  */
    protected $expires;

    protected $throttle;

    /**
     * TokenRepository constructor.
     *
     * @param ConnectionInterface $connection
     * @param string              $passwordlessTable
     * @param string              $hashKey
     * @param string              $expires
     */
    public function __construct(ConnectionInterface $connection, string $passwordlessTable, string $hashKey, string $expires)
    {
        $this->databaseConnection = $connection;
        $this->table = $passwordlessTable;
        $this->hashKey = $hashKey;
        $this->expires = $expires;
    }

    /**
     * Create new token
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     *
     * @return string
     */
    public function create(CanUsePasswordlessAuthentication $user): string
    {
        if ($this->recentlyCreatedToken($user)) {
            return null;
        }

        $this->delete($user);

        $token = $this->generateToken();

        $this->getPasswordlessTable()->insert([
            'email' => $user->getEmailForMagicLink(),
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        return $token;
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     *
     * @return bool
     */
    public function recentlyCreatedToken(CanUsePasswordlessAuthentication $user): bool
    {
        $record = $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->first()
            ->toArray();

        if (!$record) {
            return false;
        }

        return $this->tokenWasRecentlyCreates($record['created_at']);
    }

    /**
     * Check if was recently created based on throttle
     *
     * @param string $createdAt
     * @return bool
     */
    private function tokenWasRecentlyCreates(string $createdAt): bool
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)
            ->addSeconds($this->throttle)
            ->isFuture();
    }

    /**
     * Token exits abd valid
     *
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     * @param string                                                   $token
     * @return bool
     */
    public function exist(CanUsePasswordlessAuthentication $user, string $token)
    {
        $result = $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->first()
            ->toArray();

        if (!$result) {
            return false;
        }

        return !$this->tokenExpired($result['created_at']) && Hash::check($token, $result['token']);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt): bool
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * @param \NorbyBaru\Passwordless\CanUsePasswordlessAuthentication $user
     *
     * @return bool
     */
    public function delete(CanUsePasswordlessAuthentication $user): bool
    {
        return (bool) $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->delete();
    }

    /**
     * Delete expired tokens
     *
     * @return bool
     */
    public function deleteExpired(): bool
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);
        return (bool) $this->getPasswordlessTable()
            ->where('created_at', '<=', $expiredAt)
            ->delete();
    }

    /**
     * @return string
     */
    protected function generateToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function getConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getPasswordlessTable()
    {
        return $this->databaseConnection->table($this->table);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return $this->table;
    }
}
