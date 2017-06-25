<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Models\Role;

/**
 * Class EloquentRoleRepository
 * @package FreddieGar\Rbac\Repositories\Eloquent
 */
class EloquentRoleRepository extends EloquentFilterBuilder implements RoleRepository
{
    /**
     * @inheritdoc
     */
    static public function create($role)
    {
        return Role::create($role)->attributesToArray();
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
    static public function updateById($id, $role)
    {
        return Role::findOrFail($id)->update($role);
    }

    /**
     * @inheritdoc
     */
    static public function deleteById($id)
    {
        return Role::findOrFail($id)->delete();
    }

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        return self::builder(Role::select(), $filters)->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function users($role_id)
    {
        return Role::findOrFail($role_id)->users->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function permissions($role_id)
    {
        $permissions = Role::findOrFail($role_id)->permissions->toArray();

        $parents = Role::findOrFail($role_id)->parents;
        foreach ($parents as $parent) {
            $permission = self::permissions($parent['id']);
            foreach ($permission as $perm) {
                array_push($permissions, $perm);
            }
        }

        return $permissions;
    }
}
