<?php


use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Models\UserRole;
use Illuminate\Http\Response;

class UserRoleManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys([
            'user_id' => 1,
            'role_id' => 1,
        ], $excludeKeys, $includeKeys);
    }

    private function jsonStructure()
    {
        return [
            'id',
            'user_id',
            'role_id',
        ];
    }

    private function assertSearchUserRole($response)
    {
        foreach ($response as $entity) {
            $this->assertInstanceOf(\stdClass::class, $entity);
            $this->assertObjectHasAttribute('id', $entity);
            $this->assertObjectHasAttribute('user_id', $entity);
            $this->assertObjectHasAttribute('role_id', $entity);
            $this->assertNotHasAttributeUserRole($entity);
        }
    }

    private function assertNotHasAttributeUserRole($entity)
    {
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $entity);
    }

    public function testUserRoleManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('user-roles'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserRoleManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('user-roles'), $this->request(), []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserRoleManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('user-roles'), $this->request(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserRoleManagerCreateEmptyError()
    {
        $this->json(HttpMethod::POST, $this->_route('user-roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->user_id);
        $this->assertNotEmpty($response->errors->role_id);
    }

    public function testUserRoleManagerCreateUserError()
    {
        $data = [
            'user_id' => null
        ];
        $this->json(HttpMethod::POST, $this->_route('user-roles'), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->user_id);
    }


    public function testUserRoleManagerCreateRoleError()
    {
        $data = [
            'role_id' => null
        ];
        $this->json(HttpMethod::POST, $this->_route('user-roles'), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->role_id);
    }

    public function testUserRoleManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('user-roles'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals($response->user_id, $this->request()['user_id']);
        $this->assertEquals($response->role_id, $this->request()['role_id']);
        $this->assertNotHasAttributeUserRole($response);
    }

    public function testUserRoleManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->user_id, 1);
        $this->assertNotEmpty($response->role_id);
        $this->assertNotHasAttributeUserRole($response);
    }

    public function testUserRoleManagerUpdateUserError()
    {
        $data = [
            'user_id' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('user-roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->user_id);
    }

    public function testUserRoleManagerUpdateRoleError()
    {
        $data = [
            'role_id' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('user-roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->role_id);
    }

    public function testUserRoleManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('user-roles', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->user_id);
        $this->assertNotEmpty($response->errors->role_id);
    }

    public function testUserRoleManagerUpdateOk()
    {
        $data = [
            'user_id' => 1,
            'role_id' => 1,
        ];
        $this->json(HttpMethod::PUT, $this->_route('user-roles', 1), $this->request(['parent_id'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->user_id, $data['user_id']);
        $this->assertEquals($response->role_id, $data['role_id']);
        $this->assertNotHasAttributeUserRole($response);
    }

    public function testUserRoleManagerPatchOk()
    {
        $data = [
            'role_id' => 3,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('user-roles', 1), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->role_id, $data['role_id']);
        $this->assertNotHasAttributeUserRole($response);
    }

    public function testUserRoleManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('user-roles', 2), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 2);
        $this->assertObjectHasAttribute('user_id', $response);
        $this->assertObjectHasAttribute('role_id', $response);
        $this->assertNotHasAttributeUserRole($response);

        $this->json(HttpMethod::GET, $this->_route('user-roles', 2), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.model_not_found', ['model' => class_basename(UserRole::class)]));

        $this->json(HttpMethod::DELETE, $this->_route('user-roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectHasAttribute('user_id', $response);
        $this->assertObjectHasAttribute('role_id', $response);
        $this->assertNotHasAttributeUserRole($response);

        $this->json(HttpMethod::GET, $this->_route('user-roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_permission', ['description' => 'Read user role']));
    }

    public function testUserRoleManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('user-roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::PATCH, $this->_route('user-roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::DELETE, $this->_route('user-roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));
    }

    public function testUserRoleManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserRoleManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testUserRoleManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'user_id' => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'role_id' => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            BlameColumn::CREATED_BY => 2,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'role_id' => 1,
            BlameColumn::CREATED_BY => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'role_id' => 9999999,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testUserRoleManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'user_id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            'role_id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSimple10()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            BlameColumn::CREATED_BY => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            FilterTrait::$FILTER_SMART_NAME => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchUserRole($response);
    }

    public function testUserRoleManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('user-roles'), [
            FilterTrait::$FILTER_SMART_NAME => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchUserRole($response);
    }
}
