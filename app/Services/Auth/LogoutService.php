<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutService
{
    public function logOut()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
}
