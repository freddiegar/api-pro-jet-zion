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
}
