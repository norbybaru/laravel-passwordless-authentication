<?php

use Illuminate\Support\Facades\Route;
use NorbyBaru\Passwordless\Controllers\PasswordlessController;

Route::get(config('passwordless.callback_url'), [PasswordlessController::class, 'loginByEmailGet'])
    ->middleware(['web', 'signed'])
    ->name('passwordless.login');


Route::post(config('passwordless.callback_url'), [PasswordlessController::class, 'loginByEmail'])
    ->middleware(['web'])
    ->name('passwordless.login_post');
