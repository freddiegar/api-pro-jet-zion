<?php

use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;

class UserEntityTest extends TestCase
{
    public function testParser(){

        $properties = $this->user();

        $user = UserEntity::load($properties);

        $this->assertEquals($properties['id'], $user->id());
        $this->assertEquals($properties['id'], $user->id());
        $this->assertEquals($properties['status'], $user->status());
        $this->assertEquals($properties['username'], $user->username());
        $this->assertEquals($properties['password'], $user->password());
        $this->assertEquals($properties['type'], $user->type());
        $this->assertEquals(base64_encode($properties['api_token']), $user->apiToken());
        $this->assertEquals($properties['last_login_at'], $user->lastLoginAt());
        $this->assertEquals($properties['last_ip_address'], $user->lastIpAddress());
        $this->assertEquals($properties[BlameColumn::CREATED_BY], $user->createdBy());
        $this->assertEquals($properties[BlameColumn::UPDATED_BY], $user->updatedBy());
        $this->assertEquals($properties[BlameColumn::DELETED_BY], $user->deletedBy());
        $this->assertEquals($properties[BlameColumn::CREATED_AT], $user->createdAt());
        $this->assertEquals($properties[BlameColumn::UPDATED_AT], $user->updatedAt());
        $this->assertEquals($properties[BlameColumn::DELETED_AT], $user->deletedAt());
    }
}
