<?php

namespace App\Managers;

use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Entities\PermissionEntity;
use FreddieGar\Rbac\Models\Permission;
use Illuminate\Http\Request;

/**
 * Class PermissionManager
 * @package App\Managers
 */
class PermissionManager extends ManagerContract
{
    use FilterTrait;

    /**
     * PermissionManager constructor.
     * @param Request $request
     * @param PermissionRepository $repository
     */
    public function __construct(Request $request, PermissionRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return Permission
     */
    public function model()
    {
        return new Permission();
    }

    /**
     * @return PermissionRepository
     */
    protected function permissionRepository()
    {
        return parent::repository();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        $permission = $this->model()->getFromCacheId($id, function () use ($id) {
            return $this->permissionRepository()->findById($id);
        });

        return PermissionEntity::load($permission)->toArray();
    }

    /**
     * @return array
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $permissions = $this->model()->getFromCacheTag($tag, function () {
            return $this->permissionRepository()->findWhere($this->filterToApply());
        });

        return PermissionEntity::toArrayMultiple($permissions);
    }

    /**
     * @inheritdoc
     */
    protected function filters()
    {
        return [
            'slug' => [
                'type' => FilterType::TEXT
            ],
            'description' => [
                'type' => FilterType::TEXT
            ],
        ];
    }
}
