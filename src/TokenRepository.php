<?php

namespace NorbyBaru\Passwordless;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TokenRepository implements TokenInterface
{
    public function __construct(
        protected ConnectionInterface $connection,
        protected string $table,
        protected string $hashKey,
        protected int $expires,
        protected int $throttle = 0
    ) {
    }

    /**
     * Create new token
     */
    public function create(CanUsePasswordlessAuthenticatable $user): ?string
    {
        if ($this->recentlyCreatedToken($user)) {
            return null;
        }

        $this->delete($user);

        $token = $this->generateToken();

        $this->getPasswordlessTable()->insert([
            'email' => $user->getEmailForMagicLink(),
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    /**
     * Determine if the given user recently created a password reset token.
     */
    public function recentlyCreatedToken(CanUsePasswordlessAuthenticatable $user): bool
    {
        $record = $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->first();

        if (! $record) {
            return false;
        }

        return $this->tokenWasRecentlyCreated($record->created_at);
    }

    /**
     * Check if was recently created based on throttle
     */
    private function tokenWasRecentlyCreated(string $createdAt): bool
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)
            ->addSeconds($this->throttle)
            ->isFuture();
    }

    /**
     * Token exits and valid
     */
    public function exist(CanUsePasswordlessAuthenticatable $user, string $token): bool
    {
        $result = $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->first();

        if (! $result) {
            return false;
        }

        return ! $this->tokenExpired($result->created_at) && Hash::check($token, $result->token);
    }

    /**
     * Determine if the token has expired.
     */
    protected function tokenExpired(string $createdAt): bool
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    public function delete(CanUsePasswordlessAuthenticatable $user): bool
    {
        return (bool) $this->getPasswordlessTable()
            ->where('email', $user->getEmailForMagicLink())
            ->delete();
    }

    public function deleteExpired(): bool
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        return (bool) $this->getPasswordlessTable()
            ->where('created_at', '<=', $expiredAt)
            ->delete();
    }

    protected function generateToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    protected function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    protected function getPasswordlessTable(): Builder
    {
        return $this->connection->table($this->table);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return $this->table;
    }
}
