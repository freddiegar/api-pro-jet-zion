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
     * @return User
     */
    static public function model()
    {
        return new User();
    }

    /**
     * @inheritdoc
     */
    static public function getUserPasswordByUsername($username)
    {
        return self::model()->where(compact('username'))->select(['id', 'password'])->firstOrFail()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateUserLastLogin($id, $user)
    {
        return self::model()->findOrFail($id)->update($user);
    }
}
