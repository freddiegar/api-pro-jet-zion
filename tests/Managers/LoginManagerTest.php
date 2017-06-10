<?php

use App\Constants\HttpMethod;
use App\Entities\UserEntity;
use Illuminate\Http\Response;

class LoginManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys($this->login(), $excludeKeys, $includeKeys);
    }

    public function testLoginManagerError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', [], $this->headers());
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

    public function testLoginManagerUsernameError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request([], ['username' => 'freddie@gar.com']), $this->headers());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testLoginManagerPasswordError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request([], ['password' => 'Abcde7890!']), $this->headers());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->response->getStatusCode());
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testLoginManagerOK()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request(), $this->headers());
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        $this->seeJsonStructure([
            UserEntity::KEY_API_TOKEN,
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->{UserEntity::KEY_API_TOKEN});
    }
}
