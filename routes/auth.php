<?php

use App\Http\Controllers\Auth\LogOutController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::domain(config('app.domain'))->middleware('guest')->group(function () {
    Volt::route('register', 'auth.register-agency')->name('register');
    Volt::route('login', 'auth.login')->name('login');
});


Route::middleware('auth')->post(
    'logout',
    [LogOutController::class, 'logout']
)->name('logout');
