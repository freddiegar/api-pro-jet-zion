<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Models\Role;

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
        return Role::create($role_permission)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function findById($id)
    {
        return Role::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function updateById($id, $role_permission)
    {
        return Role::findOrFail($id)->update($role_permission);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return Role::findOrFail($id)->delete();
    }
}
