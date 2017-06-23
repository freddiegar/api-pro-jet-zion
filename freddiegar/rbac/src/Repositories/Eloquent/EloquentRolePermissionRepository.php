<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Models\RolePermission;

/**
 * Class EloquentRolePermissionRepository
 * @package FreddieGar\Rbac\Repositories\Eloquent
 */
class EloquentRolePermissionRepository extends EloquentFilterBuilder implements RolePermissionRepository
{
    /**
     * @inheritdoc
     */
    static public function create($role_permission)
    {
        return RolePermission::create($role_permission)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function findById($id)
    {
        return RolePermission::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateById($id, $role_permission)
    {
        return RolePermission::findOrFail($id)->update($role_permission);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return RolePermission::findOrFail($id)->delete();
    }

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        $query = self::builder(RolePermission::select(), $filters);
        return $query->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function findByRole($role_id)
    {
        return RolePermission::where(compact('role_id'))->get()->toArray();
    }
}
