<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\LoginRepository;
use App\Models\User;

/**
 * Class EloquentLoginRepository
 * @package App\Repositories\Eloquent
 */
class EloquentLoginRepository implements LoginRepository
{
    /**
     * @inheritdoc
     */
    static public function updateUserLastLogin($id, $user)
    {
        return User::findOrFail($id)->update($user);
    }

    /**
     * @inheritdoc
     */
    static public function getUserPasswordByUsername($username)
    {
        return User::where(compact('username'))->select(['id', 'password'])->firstOrFail()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function getUserApiToken($id)
    {
        return User::select('api_token')->findOrFail($id)->toArray();
    }
}
