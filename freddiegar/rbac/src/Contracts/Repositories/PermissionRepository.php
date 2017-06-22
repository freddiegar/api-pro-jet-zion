<?php

namespace FreddieGar\Rbac\Contracts\Repositories;

/**
 * Interface PermissionRepository
 * @package FreddieGar\Rbac\Contracts\Repositories
 */
interface PermissionRepository
{
    /**
     * @param int $id
     * @return array
     */
    static public function findById($id);
}
