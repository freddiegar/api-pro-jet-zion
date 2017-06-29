<?php

namespace App\Managers;

use App\Entities\UserEntity;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Constants\JsonApiName;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Base\Traits\ManagerRelationshipTrait;
use FreddieGar\Rbac\Contracts\Commons\RoleRelationshipInterface;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Entities\PermissionEntity;
use FreddieGar\Rbac\Entities\RoleEntity;
use FreddieGar\Rbac\Models\Role;
use FreddieGar\Rbac\Schemas\RoleSchema;
use Illuminate\Http\Request;

/**
 * Class RoleManager
 * @package App\Managers
 */
class RoleManager extends ManagerContract implements CRUDSInterface, RoleRelationshipInterface
{
    use FilterTrait;
    use ManagerRelationshipTrait;

    /**
     * RoleManager constructor.
     * @param Request $request
     * @param RoleRepository $repository
     */
    public function __construct(Request $request, RoleRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return Role
     */
    public function model()
    {
        return new Role();
    }

    /**
     * @return RoleRepository
     */
    protected function roleRepository()
    {
        return parent::repository();
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        $role = new RoleEntity();
        $role->description($this->requestAttribute('description'));
        $id = $role->merge($this->roleRepository()->create($role->toArray(true)))->id();
        return $this->read($id);
    }

    /**
     * @inheritdoc
     */
    public function read($id)
    {
        $role = $this->model()->getFromCacheId($id, function () use ($id) {
            return $this->roleRepository()->findById($id);
        });

        return $this->response(RoleEntity::load($role));
    }

    /**
     * @inheritdoc
     */
    public function update($id)
    {
        $role = RoleEntity::load($this->requestAttribute());
        $this->roleRepository()->updateById($id, $role->toArray(true));
        return $this->read($id);
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        $role = $this->read($id);
        $this->roleRepository()->deleteById($id);
        return $role;
    }

    /**
     * @inheritdoc
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $roles = $this->model()->getFromCacheTag($tag, function () {
            return $this->roleRepository()->findWhere($this->filterToApply());
        });

        return $this->response(RoleEntity::loadMultiple($roles));
    }

    /**
     * @inheritdoc
     */
    public function users($role_id)
    {
        $tag = makeTagNameCache([__METHOD__, $role_id]);

        $users = $this->model()->getFromCacheTag($tag, function () use ($role_id) {
            return $this->roleRepository()->users($role_id);
        });

        return UserManager::response(UserEntity::loadMultiple($users));
    }

    /**
     * @inheritdoc
     */
    public function permissions($role_id)
    {
        $tag = makeTagNameCache([__METHOD__, $role_id]);

        $permissions = $this->model()->getFromCacheTag($tag, function () use ($role_id) {
            return $this->roleRepository()->permissions($role_id);
        });

        return PermissionManager::response(PermissionEntity::loadMultiple($permissions));
    }

    /**
     * @inheritdoc
     */
    static protected function schemas()
    {
        return [
            RoleEntity::class => RoleSchema::class,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            JsonApiName::DATA => [
                JsonApiName::TYPE => 'roles',
                JsonApiName::ATTRIBUTES => [
                    'description' => 'required|max:255',
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function filters()
    {
        return [
            'description' => [
                'type' => FilterType::TEXT
            ],
            BlameColumn::CREATED_BY => [
                'type' => FilterType::NUMBER
            ]
        ];
    }
}
