<?php


use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Traits\FilterTrait;
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
            $this->assertNotHasAttributeUser($user);
        }
    }

    private function assertNotHasAttributeUser($user)
    {
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

    public function testUserManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request([UserEntity::KEY_API_TOKEN]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request([], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserManagerCreateUsernameError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(['username']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->username);
    }

    public function testUserManagerCreateUsernameRepeatedError()
    {
        $user = $this->user();
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request([], ['username' => $user['username']]), $this->headers());
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
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(['password']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->password);
    }

    public function testUserManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure([
            'id',
            'status',
            'username',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, User::where('username', 'freddie@gar.com')->first()->id);
        $this->assertEquals($response->status, UserStatus::ACTIVE);
        $this->assertEquals($response->username, 'freddie@gar.com');
        $this->assertNotHasAttributeUser($response);
    }

    public function testUserManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('users', 1), $this->request(), $this->headers());
        ff($this->response->getContent());
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
        $this->assertNotHasAttributeUser($response);
    }

    public function testUserManagerUpdateStatusError()
    {
        $data = [
            'status' => 'ERROR',
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->status);
    }

    public function testUserManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request(['username', 'password', 'status']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->username);
        $this->assertNotEmpty($response->errors->password);
    }

    public function testUserManagerUpdatePartialError()
    {
        $data = [
            'status' => UserStatus::INACTIVE,
            'password' => 'NewPass',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request(['username', 'password'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->message);
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->username);
    }

    public function testUserManagerUpdateOk()
    {
        $data = [
            'status' => UserStatus::INACTIVE,
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request([], $data), $this->headers());
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
        $this->assertNotHasAttributeUser($response);
    }

    public function testUserManagerPatchOk()
    {
        $data = [
            'status' => UserStatus::SUSPENDED,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('users', 1), $this->request(['username', 'password'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
            'status',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->status, $data['status']);
        $this->assertNotHasAttributeUser($response);
    }

    public function testUserManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('users', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectHasAttribute('username', $response);
        $this->assertObjectHasAttribute('status', $response);
        $this->assertNotHasAttributeUser($response);

        $this->json(HttpMethod::GET, $this->_route('users', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.model_not_found', ['model' => class_basename(User::class)]));
    }

    public function testUserManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::PATCH, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::DELETE, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));
    }

    public function testUserManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            UserEntity::KEY_API_TOKEN => 'token_invalid_test'
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        // Exist 6 users, but one was deleted, then are 5
        $this->assertEquals(5, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'username' => 'pedro',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'username' => 'pedro',
            'status' => UserStatus::ACTIVE,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => UserStatus::ACTIVE,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_BY => 1,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT => '2015-12-01',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => UserStatus::SUSPENDED,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testUserManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple10()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple11()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'id' => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple12()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'username' => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple13()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'password' => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple14()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            'status' => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple15()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_BY => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple16()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple17()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple18()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple19()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            FilterTrait::$FILTER_SMART_NAME => 'pica',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            FilterTrait::$FILTER_SMART_NAME => 'o@marmol',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart03()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            FilterTrait::$FILTER_SMART_NAME => 1,
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart04()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            FilterTrait::$FILTER_SMART_NAME => '2015-12-01',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart05()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [
            FilterTrait::$FILTER_SMART_NAME => '',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerCache01()
    {
//        User::enableCache();
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), $this->headers());
        $response = json_decode($this->response->getContent());
        $id = $response->id;

        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());

        $data = [
            'status' => UserStatus::INACTIVE,
            'username' => 'speedy@com.co',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', $id), $this->request([], $data), $this->headers());

        $data = [
            'status' => UserStatus::SUSPENDED,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('users', $id), $this->request(['username', 'password'], $data), $this->headers());

        $data = [
            FilterTrait::$FILTER_SMART_NAME => 'peed',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ];

        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());

        $this->json(HttpMethod::DELETE, $this->_route('users', $id), $this->request(), $this->headers());
    }

    public function testUserManagerCache02()
    {
        User::disableCache();
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), $this->headers());
        $response = json_decode($this->response->getContent());
        $id = $response->id;

        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users', $id), $this->request(), $this->headers());

        $data = [
            'status' => UserStatus::INACTIVE,
            'username' => 'speedy@com.co',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', $id), $this->request([], $data), $this->headers());

        $data = [
            'status' => UserStatus::SUSPENDED,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('users', $id), $this->request(['username', 'password'], $data), $this->headers());

        $data = [
            FilterTrait::$FILTER_SMART_NAME => 'peed',
            UserEntity::KEY_API_TOKEN => $this->apiToken()
        ];

        User::enableCache();
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());
        $this->json(HttpMethod::GET, $this->_route('users'), $data, $this->headers());

        $this->json(HttpMethod::DELETE, $this->_route('users', $id), $this->request(), $this->headers());
    }
}
