<?php


use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Traits\FilterTrait;
use Illuminate\Http\Response;

class PermissionManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys([
            'slug' => 'test.permission',
            'description' => 'Permission test'
        ], $excludeKeys, $includeKeys);
    }

    private function jsonStructure()
    {
        return [
            'id',
            'description',
        ];
    }

    private function assertSearchPermission($response)
    {
        foreach ($response as $entity) {
            $this->assertInstanceOf(\stdClass::class, $entity);
            $this->assertObjectHasAttribute('id', $entity);
            $this->assertObjectHasAttribute('description', $entity);
            $this->assertNotHasAttributePermission($entity);
        }
    }

    private function assertNotHasAttributePermission($entity)
    {
        $this->assertObjectNotHasAttribute('slug', $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $entity);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $entity);
    }

    public function testPermissionManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testPermissionManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testPermissionManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testPermissionManagerCreateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt seems that is bad idea :D'
        ];
        $this->json(HttpMethod::POST, $this->_route('permissions'), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->description, 'Show user');
        $this->assertNotHasAttributePermission($response);
    }

    public function testPermissionManagerUpdateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt',
        ];
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerUpdateOk()
    {
        $data = [
            'description' => 'New description',
        ];
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerPatchOk()
    {
        $data = [
            'description' => 'Patching permission',
        ];
        $this->json(HttpMethod::PATCH, $this->_route('permissions', 1), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));
    }

    public function testPermissionManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.not_found'));

        $this->json(HttpMethod::GET, $this->_route('permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure($this->jsonStructure());
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->id, 1);
        $this->assertEquals($response->description, 'Show user');
        $this->assertNotHasAttributePermission($response);
    }

    public function testPermissionManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::PATCH, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));

        $this->json(HttpMethod::DELETE, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.method_not_allowed'));
    }

    public function testPermissionManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], []);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testPermissionManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], [UserEntity::KEY_API_TOKEN => 'token_invalid_test']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJsonStructure([
            'message',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertInstanceOf(\stdClass::class, $response);
        $this->assertEquals($response->message, trans('exceptions.credentials'));
    }

    public function testPermissionManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => 'Test',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(5, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => 'Testing',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 1,
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => 'This description not exists',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
    }

    public function testPermissionManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'id' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            'description' => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            FilterTrait::$FILTER_SMART_NAME => 'test',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(5, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [
            FilterTrait::$FILTER_SMART_NAME => '',
        ], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = json_decode($this->response->getContent());
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }
}
