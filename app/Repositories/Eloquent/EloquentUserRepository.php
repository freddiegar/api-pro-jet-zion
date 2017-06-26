<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Base\Traits\RepositoryRelationshipTrait;
use FreddieGar\Rbac\Contracts\Commons\PermissionInterface;
use FreddieGar\Rbac\Traits\PermissionTrait;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\Eloquent
 */
class EloquentUserRepository extends EloquentFilterBuilder implements UserRepository, PermissionInterface
{
    use PermissionTrait;
    use RepositoryRelationshipTrait;

    /**
     * @inheritdoc
     */
    static public function model()
    {
        return new User();
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
    static public function findById($id)
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

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        return self::builder(self::model()->select(), $filters)->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function roles($user_id)
    {
        return self::model()->findOrFail($user_id)->roles->toArray();
    }
}
