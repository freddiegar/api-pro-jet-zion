<?php


use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Constants\JsonApiName;
use FreddieGar\Base\Traits\FilterTrait;
use Illuminate\Http\Response;

class UserManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return [
            JsonApiName::DATA => [
                JsonApiName::TYPE => 'users',
                JsonApiName::ATTRIBUTES => $this->applyKeys($this->userToCreate(), $excludeKeys, $includeKeys)
            ]
        ];
    }

    private function assertSearchUser($response)
    {
        foreach ($response as $data) {
            $attributes = $data->attributes;
            $this->assertObjectHasAttribute('username', $attributes);
            $this->assertObjectHasAttribute('status', $attributes);
            $this->assertObjectHasAttribute('created', $attributes);
            $this->assertObjectHasAttribute('updated', $attributes);
            $this->assertNotHasAttributeUser($attributes);
        }
    }

    private function assertRelationshipUser($response, $type)
    {
        foreach ($response as $data) {
            $this->assertEquals($type, $data->type);
        }
    }

    private function assertNotHasAttributeUser($entity)
    {
        $this->assertObjectNotHasAttribute('password', $entity);
        $this->assertObjectNotHasAttribute('api_token', $entity);
        $this->assertObjectNotHasAttribute('type', $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $entity);
    }

    public function testUserManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testUserManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testUserManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), array_merge($this->supportedMediaType(), [
            UserEntity::KEY_API_TOKEN => 'token_invalid_test'
        ]));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testUserManagerCreateEmptyError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
    }

    public function testUserManagerCreateUsernameError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(['username']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals('username', $response->id);
    }

    public function testUserManagerCreateUsernameRepeatedError()
    {
        $user = $this->user();
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request([], ['username' => $user['username']]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CONFLICT);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
    }

    public function testUserManagerCreatePasswordError()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(['password']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals(trans('password'), $response->id);
    }

    public function testUserManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('users'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $response = $this->responseWithData();
        $this->assertEquals($response->id, User::where('username', 'freddie@gar.com')->first()->id);
        $attributes = $response->attributes;
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals('freddie@gar.com', $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
    }

    public function testUserManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('users', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $attributes = $response->attributes;
        $this->assertEquals(1, $response->id);
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals('jon@doe.com', $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
    }

    public function testUserManagerUpdateStatusError()
    {
        $data = [
            'status' => 'ERROR',
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals(trans('status'), $response->id);
    }

    public function testUserManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('users', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
    }

    public function testUserManagerUpdatePartialError()
    {
        $data = [
            'status' => UserStatus::INACTIVE,
            'password' => 'NewPass',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request(['username', 'password'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals('username', $response->id);
    }

    public function testUserManagerUpdateOk()
    {
        $data = [
            'status' => UserStatus::INACTIVE,
            'username' => 'freddie@gar.com',
        ];
        $this->json(HttpMethod::PUT, $this->_route('users', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $attributes = $response->attributes;
        $this->assertEquals(1, $response->id);
        $this->assertEquals($data['status'], $attributes->status);
        $this->assertEquals($data['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
    }

    public function testUserManagerPatchOk()
    {
        $data = [
            'status' => UserStatus::SUSPENDED,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('users', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($data['status'], $attributes->status);
        $this->assertNotHasAttributeUser($attributes);
    }

    public function testUserManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('users', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertNotHasAttributeUser($attributes);

        $this->json(HttpMethod::GET, $this->_route('users', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.model_not_found', ['model' => class_basename(User::class)]), $response->title);
    }

    public function testUserManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::PATCH, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::DELETE, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);
    }

    public function testUserManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [], $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testUserManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [], array_merge($this->supportedMediaType(), [
            UserEntity::KEY_API_TOKEN => 'token_invalid_test'
        ]));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testUserManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        // Exist 6 users, but one was deleted, then are 5
        $this->assertEquals(5, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'username' => 'pedro',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'username' => 'pedro',
            'status' => UserStatus::ACTIVE,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => UserStatus::ACTIVE,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_BY => 1,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT => '2015-12-01',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => UserStatus::SUSPENDED,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
    }

    public function testUserManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => UserStatus::ACTIVE,
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(3, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '2015-01-01',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple10()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '2016-12-31',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(4, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple11()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'id' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple12()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'username' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple13()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'password' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple14()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            'status' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple15()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_BY => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple16()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple17()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple18()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSimple19()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MIN_SUFFIX => '',
            BlameColumn::CREATED_AT . FilterType::BETWEEN_MAX_SUFFIX => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'pica',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'o@marmol',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart03()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 1,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart04()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => '2015-12-01',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerShowSmart05()
    {
        $this->json(HttpMethod::GET, $this->_route('users'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchUser($response);
    }

    public function testUserManagerRelationShip01()
    {
        $relationship = 'roles';
        $this->json(HttpMethod::GET, $this->_route('users', 1, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertRelationshipUser($response, $relationship);
    }

    public function testUserManagerCacheEnable()
    {
        User::enableCache();
        User::setTag(User::class);

        $dataCreate = $this->request();
        $this->json(HttpMethod::POST, $this->_route('users'), $dataCreate, $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $response = $this->responseWithData();
        $attributes = $response->attributes;
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals($dataCreate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);

        $id = $response->id;

        $this->assertEquals(true, User::hasInCacheId($id));

        $this->json(HttpMethod::GET, $this->_route('users', $id), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals($dataCreate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(true, User::hasInCacheId($id));

        $dataUpdate = $this->request([], [
            'status' => UserStatus::INACTIVE,
            'username' => 'speedy@com.co',
            'password' => 'NewTest',
        ]);
        $this->json(HttpMethod::PUT, $this->_route('users', $id), $dataUpdate, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $attributes->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(true, User::hasInCacheId($id));

        $dataPatch = $this->request(['username', 'password'], [
            'status' => UserStatus::SUSPENDED,
        ]);
        $this->json(HttpMethod::PATCH, $this->_route('users', $id), $dataPatch, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($dataPatch[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $attributes->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(true, User::hasInCacheId($id));

        $tagCache = 'e23435cd6273aca6c0459bb27c6876458bb4cf6a69e754cb4da2159cfdff4db0';
        $this->assertEquals(false, User::hasInCacheTag($tagCache));

        $dataFind = $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'peed',
        ]);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertSearchUser($response);
        $this->assertEquals(true, User::hasInCacheId($id));
        $this->assertEquals(true, User::hasInCacheTag($tagCache));

        $user = $response[0]->attributes;
        $this->assertEquals($response[0]->id, $id);
        $this->assertEquals($dataPatch[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $user->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $user->username);

        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);


        $this->json(HttpMethod::DELETE, $this->_route('users', $id), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(false, User::hasInCacheId($id));
        $this->assertEquals(false, User::hasInCacheTag($tagCache));
    }

    public function testUserManagerCacheDisable()
    {
        User::disableCache();
        User::setTag(User::class);

        $dataCreate = $this->request();
        $this->json(HttpMethod::POST, $this->_route('users'), $dataCreate, $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $response = $this->responseWithData();
        $attributes = $response->attributes;
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals($dataCreate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);

        $id = $response->id;

        $this->assertEquals(false, User::hasInCacheId($id));

        $this->json(HttpMethod::GET, $this->_route('users', $id), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals(UserStatus::ACTIVE, $attributes->status);
        $this->assertEquals($dataCreate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(false, User::hasInCacheId($id));

        $dataUpdate = $this->request([], [
            'status' => UserStatus::INACTIVE,
            'username' => 'speedy@com.co',
            'password' => 'NewTest',
        ]);
        $this->json(HttpMethod::PUT, $this->_route('users', $id), $dataUpdate, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $attributes->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(false, User::hasInCacheId($id));

        $dataPatch = $this->request(['username', 'password'], [
            'status' => UserStatus::SUSPENDED,
        ]);
        $this->json(HttpMethod::PATCH, $this->_route('users', $id), $dataPatch, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($dataPatch[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $attributes->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $attributes->username);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(false, User::hasInCacheId($id));

        $tagCache = 'e23435cd6273aca6c0459bb27c6876458bb4cf6a69e754cb4da2159cfdff4db0';
        $this->assertEquals(false, User::hasInCacheTag($tagCache));

        $dataFind = $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'peed',
        ]);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertSearchUser($response);
        $this->assertEquals(false, User::hasInCacheId($id));
        $this->assertEquals(false, User::hasInCacheTag($tagCache));

        $user = $response[0]->attributes;
        $this->assertEquals($response[0]->id, $id);
        $this->assertEquals($dataPatch[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['status'], $user->status);
        $this->assertEquals($dataUpdate[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['username'], $user->username);

        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->json(HttpMethod::GET, $this->_route('users'), $dataFind, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);


        $this->json(HttpMethod::DELETE, $this->_route('users', $id), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($id, $response->id);
        $this->assertNotHasAttributeUser($attributes);
        $this->assertEquals(false, User::hasInCacheId($id));
        $this->assertEquals(false, User::hasInCacheTag($tagCache));
    }
}
