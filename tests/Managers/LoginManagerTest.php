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

    public function testLoginError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', [], $this->headers());
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

    public function testLoginUsernameError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request([], ['username' => 'freddie@gar.com']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testLoginPasswordError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request([], ['password' => 'Abcde7890!']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
    }

    public function testLoginOK()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/login', $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            UserEntity::KEY_API_TOKEN,
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->{UserEntity::KEY_API_TOKEN});
    }
}
