<?php

namespace FreddieGar\Rbac\Repositories\Eloquent;

use FreddieGar\Base\Repositories\Eloquent\EloquentFilterBuilder;
use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Models\Permission;

/**
 * Class EloquentPermissionRepository
 * @package FreddieGar\Rbac\Repositories\Eloquent
 */
class EloquentPermissionRepository extends EloquentFilterBuilder implements PermissionRepository
{
    /**
     * @inheritdoc
     */
    static public function findById($id)
    {
        return Permission::findOrFail($id)->attributesToArray();
    }

    /**
     * @inheritdoc
     */
    static public function findBySlug($slug)
    {
        return Permission::where(compact('slug'))->firstOrFail()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function findWhere($filters)
    {
        $query = self::builder(Permission::select(), $filters);
        return $query->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    static public function getSlugById($id)
    {
        return Permission::whereIn('id', $id)->get(['slug'])->toArray();
    }
}
