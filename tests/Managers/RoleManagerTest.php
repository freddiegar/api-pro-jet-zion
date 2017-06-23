<?php


use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Models\Role;
use Illuminate\Http\Response;

class RoleManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys(['description' => 'Proff role'], $excludeKeys, $includeKeys);
    }

    private function jsonStructure()
    {
        return [
            'id',
            'description',
        ];
    }

    private function assertSearchRole($response)
    {
        foreach ($response as $entity) {
            $this->assertInstanceOf(\stdClass::class, $entity);
            $this->assertObjectHasAttribute('id', $entity);
            $this->assertObjectHasAttribute('description', $entity);
            $this->assertNotHasAttributeRole($entity);
        }
    }

    private function assertNotHasAttributeRole($entity)
    {
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $entity);
    }

    public function testRoleManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRoleManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRoleManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRoleManagerCreateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt seems that is bad idea :D'
        ];
        $this->json(HttpMethod::POST, $this->_route('roles'), $data, $this->headers());
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
        $this->assertNotEmpty($response->errors->description);
    }

    public function testRoleManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals($response->description, $this->request()['description']);
        $this->assertNotHasAttributeRole($response);
    }

    public function testRoleManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->description, 'Administration User');
        $this->assertNotHasAttributeRole($response);
    }

    public function testRoleManagerUpdateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt',
        ];
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->description);
    }

    public function testRoleManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'message',
            'errors',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertEquals($response->message, trans('exceptions.validation'));
        $this->assertNotEmpty($response->errors);
        $this->assertNotEmpty($response->errors->description);
    }

    public function testRoleManagerUpdateOk()
    {
        $data = [
            'description' => 'New description',
        ];
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->description, $data['description']);
        $this->assertNotHasAttributeRole($response);
    }

    public function testRoleManagerPatchOk()
    {
        $data = [
            'description' => 'Patching role',
        ];
        $this->json(HttpMethod::PATCH, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->description, $data['description']);
        $this->assertNotHasAttributeRole($response);
    }

    public function testRoleManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'id',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertNotHasAttributeRole($response);

        $this->json(HttpMethod::GET, $this->_route('roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.model_not_found', ['model' => class_basename(Role::class)]));
    }

    public function testRoleManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::PATCH, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::DELETE, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));
    }

    public function testRoleManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRoleManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testRoleManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => 'Test',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => 'Testing',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 2,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => 'This description not exists',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testRoleManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            'description' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            BlameColumn::CREATED_BY => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            FilterTrait::$FILTER_SMART_NAME => 'esti',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            FilterTrait::$FILTER_SMART_NAME => 2,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(2, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart03()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            FilterTrait::$FILTER_SMART_NAME => 3,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart04()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [
            FilterTrait::$FILTER_SMART_NAME => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }
}
