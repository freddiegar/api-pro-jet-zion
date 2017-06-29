<?php

namespace FreddieGar\Rbac\Schemas;

use App\Entities\UserEntity;
use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Class UserSchema
 * @package FreddieGar\Rbac\Schemas
 */
class UserSchema extends SchemaProvider
{
    protected $resourceType = 'users';

    /**
     * Get resource identity.
     *
     * @param object $user
     *
     * @return string
     */
    public function getId($user)
    {
        /** @var UserEntity $user */
        return $user->id();
    }

    /**
     * Get resource attributes.
     *
     * @param object $user
     *
     * @return array
     */
    public function getAttributes($user)
    {
        /** @var UserEntity $user */
        return [
            'username' => $user->username(),
            'status' => $user->status(),
            'created' => $user->createdAt(),
            'updated' => $user->updatedAt(),
        ];
    }

    public function getResourceLinks($user)
    {
        return [];
    }
}
