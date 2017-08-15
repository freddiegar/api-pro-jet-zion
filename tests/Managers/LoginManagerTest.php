<?php

use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Constants\JsonApiName;
use Illuminate\Http\Response;

class LoginManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return [
            JsonApiName::DATA => [
                JsonApiName::TYPE => 'login',
                JsonApiName::ATTRIBUTES => $this->applyKeys($this->login(), $excludeKeys, $includeKeys)
            ]
        ];
    }

    public function testLoginManagerHeaderError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], array_merge($this->headers(), [UserEntity::KEY_API_TOKEN_HEADER => 'token_header_error_test']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginManagerHeaderOk()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginManagerAuthorizarionHeaderError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], array_merge($this->supportedMediaType(), [
            UserEntity::KEY_AUTHORIZATION_HEADER => 'token_authorization_header_error_test',
        ]));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);

    }

    public function testLoginManagerAuthorizarionHeaderOk()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], array_merge($this->supportedMediaType(), [
            UserEntity::KEY_AUTHORIZATION_HEADER => $this->apiToken(),
        ]));
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginManagerAuthorizarionBasicHeaderError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], array_merge($this->supportedMediaType(), [
            UserEntity::KEY_AUTHORIZATION_HEADER => 'Basic token_authorization_header_error_test',
        ]));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);

    }

    public function testLoginManagerAuthorizarionBasicHeaderOk()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], array_merge($this->supportedMediaType(), [
            UserEntity::KEY_AUTHORIZATION_HEADER => 'Basic ' . $this->apiToken(),
        ]));
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginManagerError()
    {
        $this->json(HttpMethod::POST, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
    }

    public function testLoginManagerUsernameError()
    {
        $this->json(HttpMethod::POST, $this->_route('login'), $this->request([], ['username' => 'freddie@gar.com']), $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.model_not_found', ['model' => class_basename(User::class)]), $response->title);
    }

    public function testLoginManagerPasswordError()
    {
        $this->json(HttpMethod::POST, $this->_route('login'), $this->request([], ['password' => 'Abcde7890!']), $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testLoginManagerOk()
    {
        $this->json(HttpMethod::POST, $this->_route('login'), $this->request(), $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            UserEntity::KEY_API_TOKEN,
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertNotEmpty($response->{UserEntity::KEY_API_TOKEN});
    }
}
