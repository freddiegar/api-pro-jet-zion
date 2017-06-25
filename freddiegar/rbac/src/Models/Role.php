<?php

namespace FreddieGar\Rbac\Models;

use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Contracts\Interfaces\BlameControlInterface;
use FreddieGar\Base\Contracts\Interfaces\CacheControlInterface;
use FreddieGar\Base\Traits\BlameControlTrait;
use FreddieGar\Base\Traits\CacheControlTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Role
 *
 * @method static Model create(array $attributes = [])
 * @method static Model select(array $columns = ['*'])
 * @method static Model|Collection findOrFail($id, $columns = ['*'])
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 *
 * @package FreddieGar\Rbac\Models
 */
class Role extends Model implements BlameControlInterface, CacheControlInterface
{
    use SoftDeletes;
    use BlameControlTrait;
    use CacheControlTrait;

    protected $guarded = [];

    protected $dates = [
        BlameColumn::CREATED_AT,
        BlameColumn::UPDATED_AT,
        BlameColumn::DELETED_AT,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function parents()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'role_id', 'parent_id');
    }
}
