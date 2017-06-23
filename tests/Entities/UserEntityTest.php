<?php

use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;

class UserEntityTest extends TestCase
{
    public function testUserEntityParser()
    {
        $properties = $this->user();

        $entity = UserEntity::load($properties);

        $this->assertEquals($properties['id'], $entity->id());
        $this->assertEquals($properties['status'], $entity->status());
        $this->assertEquals($properties['username'], $entity->username());
        $this->assertEquals($properties['password'], $entity->password());
        $this->assertEquals($properties['type'], $entity->type());
        $this->assertEquals(base64_encode($properties['api_token']), $entity->apiToken());
        $this->assertEquals($properties['last_login_at'], $entity->lastLoginAt());
        $this->assertEquals($properties['last_ip_address'], $entity->lastIpAddress());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $entity->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $entity->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $entity->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $entity->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $entity->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $entity->deletedAt());
    }
}
