<?php

namespace App\Http\Controllers;

use App\Entities\UserEntity;
use App\Managers\UserManager;

class UserController extends Controller
{
    /**
     * @return UserEntity
     */
    public function create()
    {
        return app(UserManager::class)->applyRules()->create()->toArray();
    }
}
