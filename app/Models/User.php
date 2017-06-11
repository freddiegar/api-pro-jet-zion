<?php

namespace App\Models;

use App\Constants\BlameColumn;
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
        BlameColumn::CREATED_BY,
        BlameColumn::UPDATED_BY,
        BlameColumn::DELETED_BY,
        BlameColumn::CREATED_AT,
        BlameColumn::UPDATED_AT,
        BlameColumn::DELETED_AT,
    ];

    protected $dates = [
        'last_login_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
