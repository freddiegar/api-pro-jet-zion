<?php


use App\Constants\HttpMethod;
use App\Entities\UserEntity;
use Illuminate\Http\Response;

class UserManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys($this->userToCreate(), $excludeKeys, $includeKeys);
    }

    public function testUserManagerError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', [], $this->headers());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerTokenError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([UserEntity::KEY_API_TOKEN]), $this->headers());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerTokenNotValidError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], [UserEntity::KEY_API_TOKEN => 'token no_valido']), $this->headers());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message, 2);
    }

    public function testUserManagerUsernameError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(['username']), $this->headers());
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerUsernameRepeatedError()
    {
        $user = $this->user();
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], ['username' => $user['username']]), $this->headers());
        $this->assertEquals(Response::HTTP_CONFLICT, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testUserManagerPasswordError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(['password']), $this->headers());
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerTypeError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(['type']), $this->headers());
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
    }

    public function testUserManagerOK()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request(), $this->headers());
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
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
    }
}
