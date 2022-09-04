# LARAVEL PASSWORDLESS AUTHENTICATION
Laravel Passwordless Authentication with Magic Link.

This package allows authentication via email link. 
It removes the need for users to provide password to authenticate but rely on user email address to send
them a login link to their inbox to follow to authenticate user securely.

**PS. Email provider must be setup correctly and working to email magic link to authenticate user**

## Installation

```sh
composer require norbybaru/passwordless-auth
```

## Publishing the config file
```sh
php artisan vendor:publish --provider="NorbyBaru\Passwordless\PasswordlessServiceProvider" --tag="passwordless-config"
```

## Preparing the database
You need to publish the migration to create table:
```sh
php artisan vendor:publish --provider="NorbyBaru\Passwordless\PasswordlessServiceProvider" --tag="passwordless-migrations"
```
After that, you need to run migrations.
```sh
php artisan migrate
```

# Basic Usage
## Preparing Model

## Setup Auth Provider


## Run Unit Test
```sh
composer test
```

## Run Code Formatter
```sh
composer fmt
```