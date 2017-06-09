<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\LoginRepository;
use App\Entities\UserEntity;
use App\Models\User;
use Illuminate\Http\Request;

class EloquentLoginRepository implements LoginRepository
{
    /**
     * @inheritdoc
     */
    static public function updateUserLastLogin(UserEntity $user, Request $request)
    {
        $user->lastLoginAt(now());
        $user->lastIpAddress($request->ip());
        $user->apiToken(randomHashing());

        return User::findOrFail($user->id())->update($user->toArray());
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
