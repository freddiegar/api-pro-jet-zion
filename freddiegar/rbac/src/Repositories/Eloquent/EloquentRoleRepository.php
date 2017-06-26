<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Base\Traits\RepositoryRelationshipTrait;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Models\Role;

/**
 * Class EloquentRoleRepository
 * @package FreddieGar\Rbac\Repositories\Eloquent
 */
class EloquentRoleRepository extends EloquentFilterBuilder implements RoleRepository
{
    use RepositoryRelationshipTrait;

    /**
     * @inheritdoc
     */
    static public function model()
    {
        return new Role();
    }

    /**
     * @inheritdoc
     */
    static public function create($role)
    {
        return self::model()->create($role)->attributesToArray();
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
    static public function updateById($id, $role)
    {
        return self::model()->findOrFail($id)->update($role);
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
    static public function findWhere($filters)
    {
        return self::builder(self::model()->select(), $filters)->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function users($role_id)
    {
        return self::model()->findOrFail($role_id)->users->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function permissions($role_id)
    {
        $permissions = self::model()->findOrFail($role_id)->permissions->toArray();

        $parents = self::model()->findOrFail($role_id)->parents;
        foreach ($parents as $parent) {
            $permission = self::permissions($parent['id']);
            foreach ($permission as $perm) {
                array_push($permissions, $perm);
            }
        }

        return $permissions;
    }
}
