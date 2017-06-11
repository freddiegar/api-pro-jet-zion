<?php

use App\Constants\BlameColumn;
use App\Constants\UserStatus;
use App\Entities\UserEntity;
use App\Models\User;

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

    public function apiToken()
    {
        // Jon Doe api_token seeder
        return 'SkRKNUpERXdKRlZ1Ykc5RlNrazRRakpTUTNCaFF6ZGtjR3hKTUhWbGJUQmpiRTVITjFwb05WSTJZblU0TVM1RGIzUTBkMGRUYldkT1kzQng=';
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
            UserEntity::KEY_API_TOKEN => $this->apiToken(),
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
