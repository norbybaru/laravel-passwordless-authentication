[![Run Unit Tests](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/run-tests.yml) [![PHPStan](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/phpstan.yml) [![Laravel Pint](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/pint.yml/badge.svg?branch=main)](https://github.com/norbybaru/laravel-passwordless-authentication/actions/workflows/pint.yml)

![PASSWORDLESS-AUTH](./loginlink.png)
# LARAVEL PASSWORDLESS AUTHENTICATION
Laravel Passwordless Authentication using Magic Link.

This package enables authentication through email links, eliminating the requirement for users to input passwords for authentication. Instead, it leverages the user's email address to send a login link to their inbox. Users can securely authenticate by clicking on this link. It's important to note that the package does not include a user interface for the authentication page; it assumes that the application's login page will be custom-built. Make sure to scaffold your login UI page accordingly to integrate seamlessly with this package.

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
Publish the migration to create required table:
```sh
php artisan vendor:publish --provider="NorbyBaru\Passwordless\PasswordlessServiceProvider" --tag="passwordless-migrations"
```
Run migrations.
```sh
php artisan migrate
```

# Basic Usage
## Preparing Model
Open the `User::class` Model and ensure to implements `NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable::class` and to add trait `NorbyBaru\Passwordless\Traits\PasswordlessAuthenticatable::class` to the class

```php
<?php

namespace App\Models;

...
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable;
use NorbyBaru\Passwordless\Traits\PasswordlessAuthenticatable;

class User extends Authenticatable implements CanUsePasswordlessAuthenticatable
{
    ...
    use Notifiable;
    use PasswordlessAuthenticatable;
    ...
}
```

## Preparing `config/passwordless.php`
Open config file `config/passwordless.php`
- Update `default_redirect_route` to the correct route name the user should land by default once authenticated in case you have different route name than `home`.
eg.
```
'default_redirect_route' => 'dashboard',
```

- Update `login_route` to the correct route name of your login page to allow redirecting user
back to that page on invalid magic link.
eg.
```
'login_route' => 'auth.login',
```

## Setup Login Routes
Update application Login routes to sen Magic Link to user

```php
<?php

use Illuminate\Support\Facades\Route;

Route::post('login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email|exists:users',
    ]);

    $status = Passwordless::magicLink()->sendLink($validated);

    return redirect()->back()->with([
        'status' => trans($message)
    ]);
});

```

## Setup Mail Provider
Make sure to have your application mail provider setup and working 100% for your Laravel application
```
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

## Setup Translations
Add file `passwordless.php` in your translations directory and copy the entry below.
Feel free to update text to suit your application needs.

```php
return [
    'sent' => 'Login link sent to inbox.',
    'throttled' => 'Login link was already sent. Please check your inbox or try again later.',
    'invalid_token' => 'Invalid link supplied. Please request new one.',
    'invalid_user' => 'Invalid user info supplied.',
    'verified' => 'Login successful.',
];
```

# Advance Usage
## Override MagicLinkNotification

To override default notification template, override method `sendAuthenticationMagicLink` in your User model which implements interface `CanUsePasswordlessAuthenticatable`

```php
public function sendAuthenticationMagicLink(string $token): void
{
    // Replace with your notification class.

    // eg. $this->notify(new SendMagicLinkNotification($token));
}
```

## Run Unit Test
```sh
composer test
```

## Run Code Formatter
```sh
composer fmt
```