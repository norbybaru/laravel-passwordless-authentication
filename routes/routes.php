<?php

use Illuminate\Support\Facades\Route;
use NorbyBaru\Passwordless\Controller\PasswordlessController;

Route::get(config('passwordless.callback_url'), [PasswordlessController::class, 'loginByEmail'])
    ->middleware(['web', 'signed'])
    ->name('passwordless.login');
