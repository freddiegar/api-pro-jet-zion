<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    /**
     * @inheritdoc
     */
    static public function create($user)
    {
        return User::create($user)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function getById($id)
    {
        return User::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
//    static public function getByStatus($status)
//    {
        // TODO: Implement getByStatus() method.
//    }

    /**
     * @inheritdoc
     */
//    static public function getByUsername($username)
//    {
        // TODO: Implement getByUsername() method.
//    }

    /**
     * @inheritdoc
     */
    static public function getByApiToken($apiToken)
    {
        return User::where('api_token', base64_decode($apiToken))->firstOrFail();
    }

    /**
     * @inheritdoc
     */
//    static public function isActive()
//    {
        // TODO: Implement isActive() method.
//    }
}
