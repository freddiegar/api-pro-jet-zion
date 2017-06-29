<?php

use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Constants\JsonApiName;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Models\Role;
use Illuminate\Http\Response;

class RoleManagerTest extends DBTestCase
{
    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return [
            JsonApiName::DATA => [
                JsonApiName::TYPE => 'roles',
                JsonApiName::ATTRIBUTES => $this->applyKeys([
                    'description' => 'Proff role'
                ], $excludeKeys, $includeKeys)
            ]
        ];
    }

    private function assertSearchRole($response)
    {
        foreach ($response as $data) {
            $attributes = $data->attributes;
            $this->assertObjectHasAttribute('description', $attributes);
            $this->assertObjectHasAttribute('created', $attributes);
            $this->assertObjectHasAttribute('updated', $attributes);
            $this->assertNotHasAttributeRole($attributes);
        }
    }

    private function assertRelationshipRole($response, $type)
    {
        foreach ($response as $data) {
            $this->assertEquals($type, $data->type);
        }
    }

    private function assertNotHasAttributeRole($attributes)
    {
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_BY, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::CREATED_AT, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::UPDATED_AT, $attributes);
        $this->assertObjectNotHasAttribute(BlameColumn::DELETED_AT, $attributes);
    }

    public function testRoleManagerCreateError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), [], $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testRoleManagerCreateTokenError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testRoleManagerCreateTokenNotValidError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), array_merge($this->supportedMediaType(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testRoleManagerCreateEmptyError()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
    }

    public function testRoleManagerCreateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt seems that is bad idea :D'
        ];
        $this->json(HttpMethod::POST, $this->_route('roles'), $data, $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals('description', $response->id);
    }

    public function testRoleManagerCreateOk()
    {
        $this->json(HttpMethod::POST, $this->_route('roles'), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $response = $this->responseWithData();
        $this->assertNotEmpty($response->id);
        $attributes = $response->attributes;
        $this->assertEquals($this->request()[JsonApiName::DATA][JsonApiName::ATTRIBUTES]['description'], $attributes->description);
        $this->assertNotHasAttributeRole($attributes);
    }

    public function testRoleManagerReadOk()
    {
        $this->json(HttpMethod::GET, $this->_route('roles', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals($response->id, 1);
        $attributes = $response->attributes;
        $this->assertEquals('Administration User', $attributes->description);
        $this->assertNotHasAttributeRole($attributes);
    }

    public function testRoleManagerUpdateDescriptionError()
    {
        $data = [
            'description' => 'Too long to testttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt',
        ];
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals($response->id, 'description');
    }

    public function testRoleManagerUpdateEmptyError()
    {
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.validation'), $response->title);
        $this->assertEquals($response->id, 'description');
    }

    public function testRoleManagerUpdateOk()
    {
        $data = [
            'description' => 'New description',
        ];
        $this->json(HttpMethod::PUT, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($data['description'], $attributes->description);
        $this->assertNotHasAttributeRole($attributes);
    }

    public function testRoleManagerPatchOk()
    {
        $data = [
            'description' => 'Patching role',
        ];
        $this->json(HttpMethod::PATCH, $this->_route('roles', 1), $this->request([], $data), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals($data['description'], $attributes->description);
        $this->assertNotHasAttributeRole($attributes);
    }

    public function testRoleManagerDeleteOk()
    {
        $this->json(HttpMethod::DELETE, $this->_route('roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithData();
        $this->assertEquals(1, $response->id);
        $attributes = $response->attributes;
        $this->assertEquals('Administration User', $attributes->description);
        $this->assertNotHasAttributeRole($attributes);

        $this->json(HttpMethod::GET, $this->_route('roles', 1), $this->request(), $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.model_not_found', ['model' => class_basename(Role::class)]), $response->title);
    }

    public function testRoleManagerShowSimpleMethodHttpError()
    {
        $this->json(HttpMethod::PUT, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::PATCH, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);

        $this->json(HttpMethod::DELETE, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);
    }

    public function testRoleManagerShowSimpleTokenError()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], $this->supportedMediaType());
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testRoleManagerShowSimpleNotValidToken()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], array_merge($this->supportedMediaType(), [UserEntity::KEY_API_TOKEN => 'token_invalid_test']));
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.credentials'), $response->title);
    }

    public function testRoleManagerShowSimpleEmpty()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(true, count($response) > 0);
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple01()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => 'Test',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple02()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => 'Testing',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple03()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple04()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 1,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple05()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => 'Testing',
            BlameColumn::CREATED_BY => 2,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple06()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => 'This description not exists',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple07()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'id' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple08()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            'description' => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSimple09()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            BlameColumn::CREATED_BY => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart01()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 'esti',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(1, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart02()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 2,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(2, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart03()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => 3,
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerShowSmart04()
    {
        $this->json(HttpMethod::GET, $this->_route('roles'), $this->filters([
            FilterTrait::$FILTER_SMART_NAME => '',
        ]), $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertEquals(0, count($response));
        $this->assertSearchRole($response);
    }

    public function testRoleManagerRelationShip01()
    {
        $relationship = 'users';
        $this->json(HttpMethod::GET, $this->_route('roles', Role::all()->sortByDesc('id')->first()->id, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertRelationshipRole($response, $relationship);
    }

    public function testRoleManagerRelationShip02()
    {
        $relationship = 'permissions';
        $this->json(HttpMethod::GET, $this->_route('roles', Role::all()->sortByDesc('id')->first()->id, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $response = $this->responseWithDataMultiple();
        $this->assertRelationshipRole($response, $relationship);
    }
}
