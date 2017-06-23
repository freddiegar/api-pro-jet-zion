<?php

namespace App\Managers;

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Entities\RolePermissionEntity;
use FreddieGar\Rbac\Models\RolePermission;
use Illuminate\Http\Request;

/**
 * Class RolePermissionManager
 * @package App\Managers
 */
class RolePermissionManager extends ManagerContract implements CRUDSInterface
{
    use FilterTrait;

    /**
     * RolePermissionManager constructor.
     * @param Request $request
     * @param RolePermissionRepository $repository
     */
    public function __construct(Request $request, RolePermissionRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return RolePermissionRepository
     */
    protected function rolePermissionRepository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function create()
    {
        $rolePermission = new RolePermissionEntity();
        $rolePermission->roleId($this->requestInput('role_id'));
        $rolePermission->permissionId($this->requestInput('permission_id'));
        $rolePermission->parentId($this->requestInput('parent_id'));
        $rolePermission->granted($this->requestInput('granted'));
        return $rolePermission->merge($this->rolePermissionRepository()->create($rolePermission->toArray(true)))->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        $rolePermission = RolePermission::getFromCacheId($id, function () use ($id) {
            return $this->rolePermissionRepository()->findById($id);
        });

        return RolePermissionEntity::load($rolePermission)->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        $rolePermission = RolePermissionEntity::load($this->requestInput());
        $this->rolePermissionRepository()->updateById($id, $rolePermission->toArray(true));
        return $this->read($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $rolePermission = $this->read($id);
        $this->rolePermissionRepository()->deleteById($id);
        return $rolePermission;
    }

    /**
     * @return array
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $rolePermissions = RolePermission::getFromCacheTag($tag, function () {
            return $this->rolePermissionRepository()->findWhere($this->filterToApply());
        });

        return RolePermissionEntity::toArrayMultiple($rolePermissions);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [
            'role_id' => 'required|numeric',
            'permission_id' => 'required_without:parent_id|both_not_filled:parent_id|numeric',
            'parent_id' => 'required_without:permission_id|both_not_filled:permission_id|numeric',
            'granted' => 'required|boolean',
        ];
    }

    /**
     * @return array
     */
    protected function filters()
    {
        return [
            'role_id' => [
                'type' => FilterType::NUMBER
            ],
            'permission_id' => [
                'type' => FilterType::NUMBER
            ],
            'parent_id' => [
                'type' => FilterType::NUMBER
            ],
            'granted' => [
                'type' => FilterType::NUMBER
            ],
            BlameColumn::CREATED_BY => [
                'type' => FilterType::NUMBER
            ]
        ];
    }
}
