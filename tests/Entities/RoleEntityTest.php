<?php

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Rbac\Entities\RoleEntity;

class RoleEntityTest extends TestCase
{
    public function role()
    {
        return array_merge([
            'id' => 1,
            'description' => 'Test role',
        ], $this->blame());
    }

    public function testRoleEntityParser(){

        $properties = $this->role();

        $role = RoleEntity::load($properties);

        $this->assertEquals($properties['id'], $role->id());
        $this->assertEquals($properties['description'], $role->description());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $role->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $role->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $role->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $role->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $role->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $role->deletedAt());
    }
}
