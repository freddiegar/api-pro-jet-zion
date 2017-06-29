<?php

namespace FreddieGar\Rbac\Schemas;

use FreddieGar\Rbac\Entities\PermissionEntity;
use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Class PermissionSchema
 * @package FreddieGar\Rbac\Schemas
 */
class PermissionSchema extends SchemaProvider
{
    protected $resourceType = 'permissions';

    /**
     * Get resource identity.
     *
     * @param object $permission
     *
     * @return string
     */
    public function getId($permission)
    {
        /** @var PermissionEntity $permission */
        return $permission->id();
    }

    /**
     * Get resource attributes.
     *
     * @param object $permission
     *
     * @return array
     */
    public function getAttributes($permission)
    {
        /** @var PermissionEntity $permission */
        return [
            'description' => $permission->description(),
            'created' => $permission->createdAt(),
            'updated' => $permission->updatedAt(),
        ];
    }

    public function getResourceLinks($role)
    {
        return [];
    }
}
