<?php

namespace App\Entities;

use App\Contracts\Commons\EntityContract;
use App\Traits\LoaderTrait;
use App\Traits\ToArrayTrait;

/**
 * Class UserEntity
 *
 * @property int $id
 * @property string $status
 * @property string $username
 * @property string $password
 * @property string $type
 * @property string $api_token
 * @property string $last_login_at
 * @property string $last_ip_address
 *
 * @package App\Entities
 */
class UserEntity extends EntityContract
{
    use LoaderTrait;
    use ToArrayTrait;

    /**
     * UserEntity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->load($data, $this->fields());
    }

    /**
     * @return array
     */
    protected function fields()
    {
        return [
            'id',
            'status',
            'username',
            'password',
            'type',
            'api_token',
            'last_login_at',
            'last_ip_address',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function apiToken()
    {
        return $this->api_token;
    }

    /**
     * @return string
     */
    public function lastLoginAt()
    {
        return $this->last_login_at;
    }

    /**
     * @return string
     */
    public function lastIpAddress()
    {
        return $this->last_ip_address;
    }
}
