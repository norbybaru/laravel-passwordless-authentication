<?php namespace NorbyBaru\Passwordless;


use Illuminate\Support\Str;

/**
 * Class PasswordlessManager
 * @package NorbyBaru\Passwordless
 */
class PasswordlessManager
{
    /** @var \Illuminate\Contracts\Foundation\Application  */
    protected $app;

    /**
     * PasswordlessManager constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return \NorbyBaru\Passwordless\MagicLink
     */
    public function magicLink(): MagicLink
    {
        $config =  $this->getPasswordlessConfig();

        return new MagicLink(
            $this->createTokenRepository(),
            $this->app['auth']->createUserProvider('users')
        );
    }

    protected function createTokenRepository()
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $config =  $this->getPasswordlessConfig();

        return new TokenRepository(
            $this->app['db']->connection(),
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle']
        );
    }

    /**
     * @return mixed
     */
    protected function getAuthProvider()
    {
        return $this->app['config']['auth.providers.users'];
    }


    /**
     * @return array
     */
    protected function getPasswordlessConfig()
    {
        return $this->app['config']->get('passwordless');
    }
}
