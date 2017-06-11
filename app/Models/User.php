<?php

namespace App\Models;

use App\Traits\BlameTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use SoftDeletes;
    use BlameTrait;


    protected $fillable = [
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

    protected $dates = [
        'last_login_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
