<?php

use App\Entities\UserEntity;

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
        $this->assertEquals($properties['created_by'], $user->createdBy(), 'Error text');
        $this->assertEquals($properties['updated_by'], $user->updatedBy());
        $this->assertEquals($properties['deleted_by'], $user->deletedBy());
        $this->assertEquals($properties['created_at'], $user->createdAt());
        $this->assertEquals($properties['updated_at'], $user->updatedAt());
        $this->assertEquals($properties['deleted_at'], $user->deletedAt());
    }
}
