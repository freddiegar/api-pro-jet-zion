<?php

namespace App\Http\Controllers;

use App\Managers\UserManager;

class UserController extends Controller
{
    /**
     * @return array
     */
    public function create()
    {
        return app(UserManager::class)->applyRules()->create();
    }
}
