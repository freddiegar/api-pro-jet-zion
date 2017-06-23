<?php

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Rbac\Entities\UserRoleEntity;

class UserRoleEntityTest extends TestCase
{
    public function userRole()
    {
        return array_merge([
            'id' => 1,
            'user_id' => 2,
            'role_id' => 3,
        ], $this->blame());
    }

    public function testRoleEntityParser()
    {

        $properties = $this->userRole();

        $entity = UserRoleEntity::load($properties);

        $this->assertEquals($properties['id'], $entity->id());
        $this->assertEquals($properties['user_id'], $entity->userId());
        $this->assertEquals($properties['role_id'], $entity->roleId());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $entity->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $entity->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $entity->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $entity->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $entity->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $entity->deletedAt());
    }
}
