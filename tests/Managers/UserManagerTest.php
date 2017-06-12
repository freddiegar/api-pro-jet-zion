<?php


use App\Constants\BlameColumn;
use App\Constants\HttpMethod;
use App\Constants\UserStatus;
use App\Entities\UserEntity;
use Illuminate\Http\Response;

class UserManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys($this->userToCreate(), $excludeKeys, $includeKeys);
    }

    public function testUserManagerCreateError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([UserEntity::KEY_API_TOKEN]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], [UserEntity::KEY_API_TOKEN => 'token no_valido']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerCreateUsernameError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(['username']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerCreateUsernameRepeatedError()
    {
        $user = $this->user();
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], ['username' => $user['username']]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CONFLICT);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testUserManagerCreatePasswordError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(['password']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerCreateOK()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
            'username',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 2);
        $this->assertEquals($response->username, 'freddie@gar.com');
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertObjectNotHasAttribute('api_token', $response);
        $this->assertObjectNotHasAttribute('type', $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $response);
    }

    public function testUserManagerReadOK()
    {
        $this->json(HttpMethod::GET, 'http://localhost/api/v1/user/read/1', $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
            'status',
            'username',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->status, UserStatus::ACTIVE);
        $this->assertEquals($response->username, 'jon@doe.com');
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertObjectNotHasAttribute('api_token', $response);
        $this->assertObjectNotHasAttribute('type', $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $response);
    }

    public function testUserManagerUpdateStatusError()
    {
        $data = [
            'status' => 'ERROR',
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, 'http://localhost/api/v1/user/update/1', $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerUpdateEmptyOk()
    {
        $this->json(HttpMethod::PUT, 'http://localhost/api/v1/user/update/1', $this->request(['username', 'password', 'status']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
    }

    public function testUserManagerUpdateOK()
    {
        $data = [
            'status' => UserStatus::INACTIVE,
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, 'http://localhost/api/v1/user/update/1', $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
            'status',
            'username',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->status, $data['status']);
        $this->assertEquals($response->username, $data['username']);
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertObjectNotHasAttribute('api_token', $response);
        $this->assertObjectNotHasAttribute('type', $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $response);
    }

    public function testUserManagerDeleteOK()
    {
        $this->json(HttpMethod::DELETE, 'http://localhost/api/v1/user/delete/1', $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectNotHasAttribute('username', $response);
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertObjectNotHasAttribute('status', $response);
        $this->assertObjectNotHasAttribute('api_token', $response);
        $this->assertObjectNotHasAttribute('type', $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $response);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $response);
    }
}
