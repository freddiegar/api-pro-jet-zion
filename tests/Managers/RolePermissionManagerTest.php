<?php


use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Models\RolePermission;
use Illuminate\Http\Response;

class RolePermissionManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys([
            'role_id' => 1,
            'permission_id' => 1,
            'parent_id' => 1,
            'granted' => true,
        ], $excludeKeys, $includeKeys);
    }

    private function jsonStructure()
    {
        return [
            'id',
            'role_id',
            'permission_id',
            'granted',
        ];
    }

    private function assertSearchRolePermission($response)
    {
        foreach ($response as $entity) {
            $this->assertInstanceOf(\stdClass::class, $entity);
            $this->assertObjectHasAttribute('id', $entity);
            $this->assertObjectHasAttribute('role_id', $entity);
            if (isset($entity->permission_id)) {
                $this->assertObjectNotHasAttribute('parent_id', $entity);
            } else if (isset($entity->parent_id)) {
                $this->assertObjectNotHasAttribute('permission_id', $entity);
            } else {
                $this->assertEquals(true, false);
            }
            $this->assertObjectHasAttribute('granted', $entity);
            $this->assertNotHasAttributeRolePermission($entity);
        }
    }

    private function assertNotHasAttributeRolePermission($entity)
    {
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $entity);
    }

    public function testRolePermissionManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRolePermissionManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request(), []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRolePermissionManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRolePermissionManagerCreateRoleError()
    {
        $data = [
            'role_id' => null
        ];
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request([], $data), $this->headers());
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
        $this->assertNotEmpty($response->errors->role_id);
    }

    public function testRolePermissionManagerCreatePermissionError()
    {
        $data = [
            'permission_id' => null,
            'parent_id' => null,
        ];
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request([], $data), $this->headers());
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
        $this->assertNotEmpty($response->errors->permission_id);
        $this->assertNotEmpty($response->errors->parent_id);
    }

    public function testRolePermissionManagerCreateParentError()
    {
        $data = [
            'parent_id' => null,
            'permission_id' => null,
        ];
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request([], $data), $this->headers());
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
        $this->assertNotEmpty($response->errors->parent_id);
        $this->assertNotEmpty($response->errors->permission_id);
    }

    public function testRolePermissionManagerCreateGrantedError()
    {
        $data = [
            'granted' => null,
        ];
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request([], $data), $this->headers());
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
        $this->assertNotEmpty($response->errors->granted);
    }

    public function testRolePermissionManagerCreatePermissionParentError()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request(), $this->headers());
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
        $this->assertNotEmpty($response->errors->permission_id);
        $this->assertNotEmpty($response->errors->parent_id);
    }

    public function testRolePermissionManagerCreatePermissionOk()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request(['parent_id']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals($response->role_id, $this->request()['role_id']);
        $this->assertEquals($response->permission_id, $this->request()['permission_id']);
        $this->assertEquals($response->granted, $this->request()['granted']);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerCreateParentOk()
    {
        $this->json(HttpMethod::POST, $this->_route('role-permissions'), $this->request(['permission_id']), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure([
            'id',
            'role_id',
            'parent_id',
            'granted',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals($response->role_id, $this->request()['role_id']);
        $this->assertEquals($response->parent_id, $this->request()['parent_id']);
        $this->assertEquals($response->granted, $this->request()['granted']);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->role_id, 1);
        $this->assertEquals($response->permission_id, 1);
        $this->assertEquals($response->granted, 1);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerUpdateRoleError()
    {
        $data = [
            'role_id' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request([], $data), $this->headers());
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

    public function testRolePermissionManagerUpdatePermissionError()
    {
        $data = [
            'permission_id' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->permission_id);
    }

    public function testRolePermissionManagerUpdateParentError()
    {
        $data = [
            'parent_id' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->parent_id);
    }

    public function testRolePermissionManagerUpdateGrantedError()
    {
        $data = [
            'granted' => null,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->granted);
    }

    public function testRolePermissionManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->role_id);
        $this->assertNotEmpty($response->errors->permission_id);
        $this->assertNotEmpty($response->errors->parent_id);
        $this->assertNotEmpty($response->errors->granted);
    }

    public function testRolePermissionManagerUpdateOnlyPermissionOk()
    {
        $data = [
            'role_id' => 1,
            'permission_id' => 1,
            'granted' => 1,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request(['parent_id'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->role_id, $data['role_id']);
        $this->assertEquals($response->permission_id, $data['permission_id']);
        $this->assertEquals($response->granted, $data['granted']);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerUpdateOnlyParentOk()
    {
        $data = [
            'role_id' => 1,
            'parent_id' => 1,
            'granted' => 1,
        ];
        $this->json(HttpMethod::PUT, $this->_route('role-permissions', 1), $this->request(['permission_id'], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->role_id, $data['role_id']);
        $this->assertEquals($response->parent_id, $data['parent_id']);
        $this->assertEquals($response->granted, $data['granted']);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerPatchOk()
    {
        $data = [
            'granted' => 0,
        ];
        $this->json(HttpMethod::PATCH, $this->_route('role-permissions', 1), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->granted, $data['granted']);
        $this->assertNotHasAttributeRolePermission($response);
    }

    public function testRolePermissionManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('role-permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectHasAttribute('role_id', $response);
        $this->assertObjectHasAttribute('permission_id', $response);
        $this->assertObjectHasAttribute('granted', $response);
        $this->assertNotHasAttributeRolePermission($response);

        $this->json(HttpMethod::GET, $this->_route('role-permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.model_not_found', ['model' => class_basename(RolePermission::class)]));
    }

    public function testRolePermissionManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('role-permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::PATCH, $this->_route('role-permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::DELETE, $this->_route('role-permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));
    }

    public function testRolePermissionManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRolePermissionManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRolePermissionManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'role_id' => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(5, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'permission_id' => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'parent_id' => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            BlameColumn::CREATED_BY => 2,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(6, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'role_id' => 1,
            BlameColumn::CREATED_BY => 2,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'role_id' => 9999999,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testRolePermissionManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'role_id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'permission_id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple10()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'parent_id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple11()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            'granted' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSimple12()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            BlameColumn::CREATED_BY => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            FilterTrait::$FILTER_SMART_NAME => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchRolePermission($response);
    }

    public function testRolePermissionManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('role-permissions'), [
            FilterTrait::$FILTER_SMART_NAME => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRolePermission($response);
    }
}
