<?php

use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\JsonApiName;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Middlewares\SupportedMediaTypeMiddleware;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        Artisan::call('cache:clear');
    }

    public function login()
    {
        return [
            'username' => 'jon@doe.com',
            'password' => 'Admin1234/'
        ];
    }

    public function apiToken()
    {
        // api_token to TestUser (Jon Doe)
        return 'SkRKNUpERXdKRlZ1Ykc5RlNrazRRakpTUTNCaFF6ZGtjR3hKTUhWbGJUQmpiRTVITjFwb05WSTJZblU0TVM1RGIzUTBkMGRUYldkT1kzQng=';
    }

    public function _route($route, $id = null, $relationship = null)
    {
        $url = 'http://localhost/api/v1/' . $route;
        $url .= $id ? '/' . $id : '';
        $url .= $relationship ? '/' . $relationship : '';

        return ltrim($url, '/');
    }

    public function blame()
    {
        return [
            BlameColumn::CREATED_BY => 1,
            BlameColumn::UPDATED_BY => 2,
            BlameColumn::DELETED_BY => 3,
            BlameColumn::CREATED_AT => '2015-01-01 12:33:24',
            BlameColumn::UPDATED_AT => '2016-11-02 15:26:00',
            BlameColumn::DELETED_AT => '2017-06-03 09:34:28',
        ];
    }

    public function user()
    {
        return array_merge([
            'id' => 1,
            'status' => UserStatus::ACTIVE,
            'username' => 'jon@doe.com',
            'password' => 'Admin1234/',
            'type' => User::class,
            'api_token' => $this->apiToken(),
            'last_login_at' => now(),
            'last_ip_address' => '127.0.0.1',
        ], $this->blame());
    }

    public function userToCreate()
    {
        return [
            'username' => 'freddie@gar.com',
            'password' => 'Admin1234/',
        ];
    }

    public function headers()
    {
        return array_merge($this->supportedMediaType(), [
            UserEntity::KEY_API_TOKEN_HEADER => $this->apiToken(),
        ]);
    }

    public function supportedMediaType()
    {
        return [
            'CONTENT_TYPE' => SupportedMediaTypeMiddleware::MEDIA_TYPE_SUPPORTED,
        ];
    }

    public function applyKeys(array $request = [], array $excludeKeys = [], array $includeKeys = [])
    {
        if (count($excludeKeys) > 0) {
            foreach ($excludeKeys as $excludeKey) {
                if (is_array($excludeKey)) {
                    $request = $this->applyKeys($request, $excludeKey);
                } else {
                    unset($request[$excludeKey]);
                }
            }
        }

        if (count($includeKeys) > 0) {
            $request = array_merge($request, $includeKeys);
        }

        return $request;
    }

    public function responseWithData()
    {
        $this->seeJsonStructure([
            JsonApiName::DATA
        ]);

        $response = json_decode($this->response->getContent());

        $this->assertObjectHasAttribute(JsonApiName::TYPE, $response->data);
        $this->assertObjectHasAttribute(JsonApiName::ID, $response->data);
        $this->assertObjectHasAttribute(JsonApiName::ATTRIBUTES, $response->data);

        return $response->data;
    }

    public function responseWithDataMultiple()
    {
        $this->seeJsonStructure([
            JsonApiName::DATA
        ]);

        $response = json_decode($this->response->getContent());

        foreach ($response->data as $data) {
            $this->assertObjectHasAttribute(JsonApiName::TYPE, $data);
            $this->assertObjectHasAttribute(JsonApiName::ID, $data);
            $this->assertObjectHasAttribute(JsonApiName::ATTRIBUTES, $data);
        }

        return $response->data;
    }

    public function responseWithErrors()
    {
        $this->seeJsonStructure([
            JsonApiName::ERRORS
        ]);

        $response = json_decode($this->response->getContent());
        $error = $response->errors[0];

        $this->assertObjectHasAttribute(JsonApiName::STATUS, $error);
        $this->assertObjectHasAttribute(JsonApiName::TITLE, $error);
        $this->assertObjectHasAttribute(JsonApiName::DETAIL, $error);

        return $error;
    }

    public function filters(array $fields = [])
    {
        $filters = [];

        foreach ($fields as $field => $value) {
            $filters[$field] = $value;
        }

        return [ManagerContract::WRAPPER_FILTERS => $filters];
    }

}
