<?php

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Rbac\Entities\RolePermissionEntity;

class RolePermissionEntityTest extends TestCase
{
    public function rolePermission()
    {
        return array_merge([
            'id' => 1,
            'role_id' => 2,
            'permission_id' => 3,
            'parent_id' => 4,
            'granted' => 1,
        ], $this->blame());
    }

    public function testRoleEntityParser()
    {
        $properties = $this->rolePermission();

        $entity = RolePermissionEntity::load($properties);

        $this->assertEquals($properties['id'], $entity->id());
        $this->assertEquals($properties['role_id'], $entity->roleId());
        $this->assertEquals($properties['permission_id'], $entity->permissionId());
        $this->assertEquals($properties['parent_id'], $entity->parentId());
        $this->assertEquals($properties['granted'], $entity->granted());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $entity->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $entity->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $entity->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $entity->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $entity->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $entity->deletedAt());
    }
}
