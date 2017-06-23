<?php

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Rbac\Entities\PermissionEntity;

class PermissionEntityTest extends TestCase
{
    public function permission()
    {
        return array_merge([
            'id' => 1,
            'slug' => 'test.permission',
            'description' => 'Test permission',
        ], $this->blame());
    }

    public function testRoleEntityParser(){

        $properties = $this->permission();

        $entity = PermissionEntity::load($properties);

        $this->assertEquals($properties['id'], $entity->id());
        $this->assertEquals($properties['slug'], $entity->slug());
        $this->assertEquals($properties['description'], $entity->description());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $entity->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $entity->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $entity->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $entity->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $entity->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $entity->deletedAt());
    }
}
