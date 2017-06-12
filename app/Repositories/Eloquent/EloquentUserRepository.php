<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\Eloquent
 */
class EloquentUserRepository implements UserRepository
{
    /**
     * @return User
     */
    static public function model()
    {
        static $model;
        return $model = (is_null($model) ? new User() : $model);
    }

    /**
     * @inheritdoc
     */
    static public function create($user)
    {
        return self::model()->create($user)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function getById($id)
    {
        return self::model()->findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateById($id, $user)
    {
        return self::model()->findOrFail($id)->update($user);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return self::model()->findOrFail($id)->delete();
    }

    /**
     * @inheritdoc
     */
    static public function getByApiToken($apiToken)
    {
        return self::model()->where('api_token', base64_decode($apiToken))->first();
    }
}
