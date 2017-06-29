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

    private function assertSearchPermission($response)
    {
        foreach ($response as $data) {
            $attributes = $data->attributes;
            $this->assertObjectHasAttribute('description', $attributes);
            $this->assertObjectHasAttribute('created', $attributes);
            $this->assertObjectHasAttribute('updated', $attributes);
            $this->assertNotHasAttributePermission($attributes);
        }
    }

    private function assertNotHasAttributePermission($attributes)
    {
        $this->assertObjectNotHasAttribute('slug', $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $attributes);
    }

    public function testPermissionManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), array_merge($this->supportedMediaType(), [UserEntity::KEY_API_TOKEN_HEADER => '']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testPermissionManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), array_merge($this->supportedMediaType(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testPermissionManagerCreateEmptyError()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerCreateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt seems that is bad idea :D'
        ];
        $this->json(HttpMethod::POST, $this->_route('permissions'), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('permissions'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals('Show user', $attributes->description);
        $this->assertNotHasAttributePermission($attributes);
    }

    public function testPermissionManagerUpdateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt',
        ];
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerUpdateOk()
    {
        $data = [
            'description' => 'New description',
        ];
        $this->json(HttpMethod::PUT, $this->_route('permissions', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerPatchOk()
    {
        $data = [
            'description' => 'Patching permission',
        ];
        $this->json(HttpMethod::PATCH, $this->_route('permissions', 1), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);
    }

    public function testPermissionManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found'), $response->title);

        $this->json(HttpMethod::GET, $this->_route('permissions', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals('Show user', $attributes->description);
        $this->assertNotHasAttributePermission($attributes);
    }

    public function testPermissionManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::PATCH, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::DELETE, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);
    }

    public function testPermissionManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testPermissionManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], array_merge($this->supportedMediaType(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testPermissionManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => 'Test',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(5, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => 'Testing',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 1,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => 'This description not exists',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'id' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            'description' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'test',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(5, count($response));
        $this->assertSearchPermission($response);
    }

    public function testPermissionManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('permissions'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchPermission($response);
    }
}
