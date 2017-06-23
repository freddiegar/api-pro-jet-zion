<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use FreddieGar\Rbac\Models\UserRole;

/**
 * Class EloquentUserRoleRepository
 * @package FreddieGar\Rbac\Repositories\Eloquent
 */
class EloquentUserRoleRepository extends EloquentFilterBuilder implements UserRoleRepository
{
    /**
     * @inheritdoc
     */
    static public function create($user_role)
    {
        return UserRole::create($user_role)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function findById($id)
    {
        return UserRole::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateById($id, $user_role)
    {
        return UserRole::findOrFail($id)->update($user_role);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return UserRole::findOrFail($id)->delete();
    }

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        $query = self::builder(UserRole::select(), $filters);
        return $query->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function roles($user_id)
    {
        return UserRole::where(compact('user_id'))->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function users($role_id)
    {
        return UserRole::where(compact('role_id'))->get()->toArray();
    }
}
