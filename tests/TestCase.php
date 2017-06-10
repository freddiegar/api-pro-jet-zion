<?php

use App\Constants\HttpMethod;
use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;
use Illuminate\Http\Response;

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

    public function login()
    {
        return [
            'username' => 'jon@doe.com',
            'password' => 'Admin1234/'
        ];
    }

    public function blame()
    {
        return [
            'created_by' => 1,
            'updated_by' => 2,
            'deleted_by' => 3,
            'created_at' => '2017-01-01 12:33:24',
            'updated_at' => '2017-01-02 15:26:00',
            'deleted_at' => null,
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
            'api_token' => 'SkRKNUpERXdKRlZ1Ykc5RlNrazRRakpTUTNCaFF6ZGtjR3hKTUhWbGJUQmpiRTVITjFwb05WSTJZblU0TVM1RGIzUTBkMGRUYldkT1kzQng=',
            'last_login_at' => now(),
            'last_ip_address' => '127.0.0.1',
        ], $this->blame());
    }

    public function userToCreate()
    {
        return [
            'username' => 'freddie@gar.com',
            'password' => 'Admin1234/',
            'type' => User::class,
            // Jon Doe api_token seeder
            UserEntity::KEY_API_TOKEN => 'SkRKNUpERXdKRlZ1Ykc5RlNrazRRakpTUTNCaFF6ZGtjR3hKTUhWbGJUQmpiRTVITjFwb05WSTJZblU0TVM1RGIzUTBkMGRUYldkT1kzQng=',
        ];
    }

    public function headers()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
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
}
