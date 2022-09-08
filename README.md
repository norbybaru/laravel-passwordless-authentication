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
Open the `User::class` Model and make sure to implements `NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable::class` and add trait `NorbyBaru\Passwordless\Traits\PasswordlessAuthenticatable::class` to the class

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
Open `config/passwordless.php` file and update `default_redirect_route` to the correct route name the user should land by default once authenticated in case you have different route name than `home`.

eg.
```
'default_redirect_route' => 'dashboard',
```

## Setup Login Routes

```php
<?php

use Illuminate\Support\Facades\Route;

Route::post('login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email|exists:users',
    ]);

    $message = Passwordless::magicLink()->sendLink($validated);

    return redirect()->back()->with([
        'status' => $message
    ]);
});

```

## Setup Mail provider

## Setup Auth Provider

# Advance Usage
## Override MagicLinkNotification

## Run Unit Test
```sh
composer test
```

## Run Code Formatter
```sh
composer fmt
```