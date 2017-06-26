<?php

namespace FreddieGar\Rbac\Schemas;

use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Class RoleSerializer
 * @package FreddieGar\Rbac\Schemas
 */
class RoleSchema extends SchemaProvider
{
    protected $resourceType = 'role';

    /**
     * Get resource identity.
     *
     * @param object $role
     *
     * @return string
     */
    public function getId($role)
    {
        return $role->id();
    }

    /**
     * Get resource attributes.
     *
     * @param object $role
     *
     * @return array
     */
    public function getAttributes($role)
    {
        return [
            'description' => $role->description(),
            'created' => $role->createdAt(),
            'updated' => $role->updatedAt(),
        ];
    }
}
