<?php

use Illuminate\Support\Facades\Route;
use NorbyBaru\Passwordless\Tests\Fixtures\TestController;

Route::get('/home', [TestController::class, 'index'])->name('home');
Route::get('/login', [TestController::class, 'index'])->middleware(['guest'])->name('login');
Route::get('/intended-redirect', [TestController::class, 'redirect'])->middleware(['auth'])->name('redirect');
