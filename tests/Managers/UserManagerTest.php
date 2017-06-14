<?php


use App\Constants\BlameColumn;
use App\Constants\FilterType;
use App\Constants\HttpMethod;
use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;
use Illuminate\Http\Response;

class UserManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys($this->userToCreate(), $excludeKeys, $includeKeys);
    }

    private function assertSearchUser($response)
    {
        foreach ($response as $user) {
            $this->assertInstanceOf(\stdClass::class, $user);
            $this->assertObjectHasAttribute('id', $user);
            $this->assertObjectHasAttribute('username', $user);
            $this->assertObjectHasAttribute('status', $user);
            $this->assertObjectNotHasAttribute('password', $user);
            $this->assertObjectNotHasAttribute('api_token', $user);
            $this->assertObjectNotHasAttribute('type', $user);
            $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $user);
            $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $user);
            $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $user);
            $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $user);
            $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $user);
            $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $user);
        }
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
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/create', $this->request([], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']), $this->headers());
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
        $this->assertEquals($response->id, User::where('username', 'freddie@gar.com')->first()->id);
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

    public function testUserManagerSearchSimpleMethodHttpError()
    {
        $this->json(HttpMethod::GET, 'http://localhost/api/v1/user/search', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);

        $this->json(HttpMethod::PUT, 'http://localhost/api/v1/user/search', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);

        $this->json(HttpMethod::DELETE, 'http://localhost/api/v1/user/search', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
    }

    public function testUserManagerSearchSimpleTokenError()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
    }

    public function testUserManagerSearchSimpleNotValidToken()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            UserEntity::KEY_API_TOKEN => 'token_invalid_test'
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
    }

    public function testUserManagerSearchSimpleEmpty()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        // Exist 6 users, but one was deleted, then are 5
        $this->assertEquals(5, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple1()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'username' => 'pedro',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple2()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'username' => 'pedro',
            'status' => UserStatus::ACTIVE,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple3()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'status' => UserStatus::ACTIVE,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple4()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_BY => 1,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple5()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT => '2015-12-01',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple6()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'status' => UserStatus::SUSPENDED,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testUserManagerSearchSimple7()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple8()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple9()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerSearchSimple10()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/user/search', [
            BlameColumn::CREATED_AT. FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }
}
