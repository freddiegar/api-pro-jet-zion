<?php


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

    public function testCreateError()
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

    public function testCreateTokenError()
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

    public function testCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], [UserEntity::KEY_API_TOKEN => 'token no_valido']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testCreateUsernameError()
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

    public function testCreateUsernameRepeatedError()
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

    public function testCreatePasswordError()
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

    public function testCreateOK()
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
        $this->assertObjectNotHasAttribute('created_by', $response);
        $this->assertObjectNotHasAttribute('updated_by', $response);
        $this->assertObjectNotHasAttribute('deleted_by', $response);
        $this->assertObjectNotHasAttribute('created_at', $response);
        $this->assertObjectNotHasAttribute('updated_at', $response);
        $this->assertObjectNotHasAttribute('deleted_at', $response);
    }

    public function testReadOK()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/read/1', $this->request(), $this->headers());
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
        $this->assertObjectNotHasAttribute('created_by', $response);
        $this->assertObjectNotHasAttribute('updated_by', $response);
        $this->assertObjectNotHasAttribute('deleted_by', $response);
        $this->assertObjectNotHasAttribute('created_at', $response);
        $this->assertObjectNotHasAttribute('updated_at', $response);
        $this->assertObjectNotHasAttribute('deleted_at', $response);
    }

    public function testUpdateStatusError()
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

    public function testUpdateEmptyOk()
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

    public function testUpdateOK()
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
        $this->assertObjectNotHasAttribute('created_by', $response);
        $this->assertObjectNotHasAttribute('updated_by', $response);
        $this->assertObjectNotHasAttribute('deleted_by', $response);
        $this->assertObjectNotHasAttribute('created_at', $response);
        $this->assertObjectNotHasAttribute('updated_at', $response);
        $this->assertObjectNotHasAttribute('deleted_at', $response);
    }

    public function xtestDeleteOK()
    {
        $this->json(HttpMethod::DELETE, 'http://localhost/api/v1/user/delete/1', $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
            'status',
            'username',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertObjectNotHasAttribute('api_token', $response);
        $this->assertObjectNotHasAttribute('type', $response);
        $this->assertObjectNotHasAttribute('created_by', $response);
        $this->assertObjectNotHasAttribute('updated_by', $response);
        $this->assertObjectNotHasAttribute('deleted_by', $response);
        $this->assertObjectNotHasAttribute('created_at', $response);
        $this->assertObjectNotHasAttribute('updated_at', $response);
        $this->assertObjectNotHasAttribute('deleted_at', $response);
    }
}
