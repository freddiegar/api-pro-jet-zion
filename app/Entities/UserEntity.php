<?php

namespace App\Entities;

use App\Contracts\Commons\EntityContract;

/**
 * Class UserEntity
 * @package App\Entities
 */
class UserEntity extends EntityContract
{
    const KEY_API_TOKEN = '_token_';

    protected $id;
    protected $status;
    protected $username;
    protected $password;
    protected $type;
    protected $api_token;
    protected $last_login_at;
    protected $last_ip_address;

    /**
     * @inheritdoc
     */
    protected function fields()
    {
        return array_merge([
            'id',
            'status',
            'username',
            'password',
            'type',
            'api_token',
            'last_login_at',
            'last_ip_address',
        ], $this->blames());
    }

    /**
     * @inheritdoc
     */
    protected function hiddens()
    {
        return array_merge([
            'password',
            'api_token',
            'type',
        ], $this->blames());
    }

    /**
     * @param null $id
     * @return string
     */
    public function id($id = null)
    {
        if (!is_null($id)) {
            $this->id = $id;
            return $this;
        }

        return $this->id;
    }

    /**
     * @param string $status
     * @return string
     */
    public function status($status = null)
    {
        if (!is_null($status)) {
            $this->status = $status;
            return $this;
        }

        return $this->status;
    }

    /**
     * @param string $username
     * @return string
     */
    public function username($username = null)
    {
        if (!is_null($username)) {
            $this->username = $username;
            return $this;
        }

        return $this->username;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function password($password = null)
    {
        if (!is_null($password)) {
            $this->password = $password;
            return $this;
        }
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = hashing($password);
        return $this;
    }

    /**
     * @param string $type
     * @return string
     */
    public function type($type = null)
    {
        if (!is_null($type)) {
            $this->type = $type;
            return $this;
        }

        return $this->type;
    }

    /**
     * @param string $api_token
     * @return string
     */
    public function apiToken($api_token = null)
    {
        if (!is_null($api_token)) {
            $this->api_token = base64_encode($api_token);
            return $this;
        }

        return $this->api_token;
    }

    /**
     * @param string $last_login_at
     * @return string
     */
    public function lastLoginAt($last_login_at = null)
    {
        if (!is_null($last_login_at)) {
            $this->last_login_at = $last_login_at;
            return $this;
        }

        return $this->last_login_at;
    }

    /**
     * @param string $last_ip_address
     * @return string
     */
    public function lastIpAddress($last_ip_address = null)
    {
        if (!is_null($last_ip_address)) {
            $this->last_ip_address = $last_ip_address;
            return $this;
        }

        return $this->last_ip_address;
    }
}
