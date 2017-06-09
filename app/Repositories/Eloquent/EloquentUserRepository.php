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
     * @param UserEntity $user
     * @return UserEntity
     */
    static public function create(UserEntity $user)
    {
        return new UserEntity(User::create($user->toArray())->attributesToArray());
    }

    /**
     * @param UserEntity $user
     * @return UserEntity
     */
    static public function updateLastLogin($user)
    {
        // TODO: Implement updateLastLogin() method.
    }

    /**
     * @param $id
     * @return UserEntity
     */
    static public function getById($id)
    {
        // TODO: Implement getById() method.
    }

    /**
     * @param $status
     * @return UserEntity
     */
    static public function getByStatus($status)
    {
        // TODO: Implement getByStatus() method.
    }

    /**
     * @param $username
     * @return UserEntity
     */
    static public function getByUsername($username)
    {
        // TODO: Implement getByUsername() method.
    }

    /**
     * @param $apiToken
     * @return UserEntity
     */
    static public function getByApiToken($apiToken)
    {
        // TODO: Implement getByApiToken() method.
    }

    /**
     * @return UserEntity
     */
    static public function isActive()
    {
        // TODO: Implement isActive() method.
    }
}
