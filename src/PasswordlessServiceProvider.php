<?php

namespace NorbyBaru\Passwordless;

use Illuminate\Support\ServiceProvider;

/**
 * Class PasswordlessServiceProvider
 */
class PasswordlessServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        $this->loadMigrations();
        $this->loadRoutes();
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'passwordless');
        $this->registerPasswordlessManager();
    }

    protected function registerPasswordlessManager()
    {
        $this->app->singleton('auth.passwordless', function () {
            return new PasswordlessManager($this->app);
        });
    }

    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
    }

    /**
     * Return config file
     *
     * @return string
     */
    protected function configPath()
    {
        return __DIR__.'/../config/passwordless.php';
    }

    /**
     * Publish config file.
     */
    protected function publishConfig()
    {
        $this->publishes([
            $this->configPath() => config_path('passwordless.php'),
        ], 'config');
    }

    /**
     * Load migration files.
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['auth.passwordless'];
    }
}
