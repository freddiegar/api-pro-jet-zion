<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\Eloquent
 */
class EloquentUserRepository extends EloquentFilterBuilder implements UserRepository
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
    static public function findById($id)
    {
        return User::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateById($id, $user)
    {
        return User::findOrFail($id)->update($user);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return User::findOrFail($id)->delete();
    }

    /**
     * @inheritdoc
     */
    static public function getByApiToken($apiToken)
    {
        return User::where('api_token', base64_decode($apiToken))->first();
    }

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        $query = self::builder(User::select(), $filters);
        return $query->get()->toArray();
    }
}
