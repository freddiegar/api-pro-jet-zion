<?php

namespace App\Http\Controllers;

use App\Managers\LoginManager;

class LoginController extends Controller
{
    /**
     * @return array
     */
    public function login()
    {
        return app(LoginManager::class)->applyRules()->login();
    }
}
