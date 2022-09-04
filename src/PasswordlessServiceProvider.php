<?php

namespace NorbyBaru\Passwordless;

use Illuminate\Support\ServiceProvider;

class PasswordlessServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        $this->publishDatabase();
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
     */
    protected function configPath(): string
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
        ], 'passwordless-config');
    }

    protected function publishDatabase()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/create_passwordless_auth_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_passwordless_auth_table.php'),
        ], 'passwordless-migrations');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['auth.passwordless'];
    }
}
