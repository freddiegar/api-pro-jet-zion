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
        $query = self::builder(Role::select(), $filters);
        return $query->get()->toArray();
    }
}
