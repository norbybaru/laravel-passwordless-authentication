<?php

namespace NorbyBaru\Passwordless;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class PasswordlessManager
{
    public function __construct(protected Application $app)
    {
    }

    public function magicLink(): MagicLink
    {
        return new MagicLink(
            $this->createTokenRepository(),
            $this->app['auth']->createUserProvider('users')
        );
    }

    protected function createTokenRepository(): TokenRepository
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $config = $this->getPasswordlessConfig();

        return new TokenRepository(
            $this->app['db']->connection(),
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle']
        );
    }

    protected function getAuthProvider(): mixed
    {
        return $this->app['config']['auth.providers.users'];
    }

    protected function getPasswordlessConfig(): array
    {
        return $this->app['config']->get('passwordless');
    }
}
