<?php

namespace FreddieGar\Rbac\Schemas;

use FreddieGar\Rbac\Entities\RoleEntity;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;
use Neomerx\JsonApi\Document\Link;
use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Class RoleSerializer
 * @package FreddieGar\Rbac\Schemas
 */
class RoleSchema extends SchemaProvider
{
    protected $resourceType = 'roles';

    /**
     * Get resource identity.
     *
     * @param object $role
     *
     * @return string
     */
    public function getId($role)
    {
        /** @var RoleEntity $role */
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
        /** @var RoleEntity $role */
        return [
            'description' => $role->description(),
            'created' => $role->createdAt(),
            'updated' => $role->updatedAt(),
        ];
    }

    public function getRelationships($role, $isPrimary, array $includeRelationships)
    {
        $userLinks = [
            LinkInterface::RELATED => new Link($this->getSelfSubUrl($role) . '/users'),
        ];

        $permissionLinks = [
            LinkInterface::RELATED => new Link($this->getSelfSubUrl($role) . '/permissions'),
        ];

        $relationships = [];

        $relationships['users'] = [
            self::LINKS => $userLinks,
        ];

        $relationships['permissions'] = [
            self::LINKS => $permissionLinks,
        ];

        return $relationships;
    }

    public function getResourceLinks($role)
    {
        return [];
    }
}
