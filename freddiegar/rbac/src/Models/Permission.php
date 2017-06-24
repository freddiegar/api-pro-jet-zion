<?php

namespace FreddieGar\Rbac\Models;

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
 * Class Permission
 *
 * @method static Model create(array $attributes = [])
 * @method static Model select(array $columns = ['*'])
 * @method static Model|Collection findOrFail($id, $columns = ['*'])
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 *
 * @package FreddieGar\Rbac\Models
 */
class Permission extends Model implements BlameControlInterface, CacheControlInterface
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
}
