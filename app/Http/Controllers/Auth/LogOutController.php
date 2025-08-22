<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\LogoutService;

class LogOutController
{
    public function index()
    {
    }

    public function logout()
    {
        return app(LogoutService::class)->logOut();
    }
}
