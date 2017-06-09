<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use App\Traits\BlameTrait;

class EloquentUserRepository implements UserRepository
{
    use BlameTrait;

    /**
     * @inheritdoc
     */
    static public function create(UserEntity $user)
    {
        return new UserEntity(User::create($user->toArray())->attributesToArray());
    }

    /**
     * @inheritdoc
     */
//    static public function getById($id)
//    {
        // TODO: Implement getById() method.
//    }

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
//    static public function getByApiToken($apiToken)
//    {
        // TODO: Implement getByApiToken() method.
//    }

    /**
     * @inheritdoc
     */
//    static public function isActive()
//    {
        // TODO: Implement isActive() method.
//    }
}
