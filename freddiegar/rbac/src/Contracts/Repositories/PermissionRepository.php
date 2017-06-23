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

    /**
     * @param string $slug
     * @return array
     */
    static public function findBySlug($slug);

    /**
     * @param array $filters
     * @return array
     */
    static public function findWhere($filters);

    /**
     * @param array $id
     * @return array
     */
    static public function getSlugById($id);
}
