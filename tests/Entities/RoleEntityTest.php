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

        $entity = RoleEntity::load($properties);

        $this->assertEquals($properties['id'], $entity->id());
        $this->assertEquals($properties['description'], $entity->description());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $entity->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $entity->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $entity->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $entity->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $entity->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $entity->deletedAt());
    }
}
