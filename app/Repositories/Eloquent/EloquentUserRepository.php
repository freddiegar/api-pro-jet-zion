<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Commons\PermissionContract;
use FreddieGar\Rbac\Traits\PermissionTrait;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\Eloquent
 */
class EloquentUserRepository extends EloquentFilterBuilder implements UserRepository, PermissionContract
{
    use PermissionTrait;

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
        return self::builder(User::select(), $filters)->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function roles($user_id)
    {
        return User::findOrFail($user_id)->roles->toArray();
    }
}
