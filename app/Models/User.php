<?php

namespace App\Models;

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Contracts\Interfaces\BlameControlInterface;
use FreddieGar\Base\Contracts\Interfaces\CacheControlInterface;
use FreddieGar\Base\Traits\BlameControlTrait;
use FreddieGar\Base\Traits\BlameEventTrait;
use FreddieGar\Base\Traits\CacheControlTrait;
use FreddieGar\Base\Traits\CacheEventTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

/**
 * Class User
 *
 * @method static Model create(array $attributes = [])
 * @method static Model select(array $columns = ['*'])
 * @method static Model|Collection findOrFail($id, $columns = ['*'])
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 *
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, BlameControlInterface, CacheControlInterface
{
    use Authenticatable;
    use Authorizable;
    use SoftDeletes;
    use BlameControlTrait;
    use CacheControlTrait;

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
        BlameColumn::CREATED_AT,
        BlameColumn::UPDATED_AT,
        BlameColumn::DELETED_AT,
    ];
}
