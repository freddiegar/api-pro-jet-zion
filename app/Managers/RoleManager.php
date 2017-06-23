<?php

namespace App\Managers;

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Entities\RoleEntity;
use FreddieGar\Rbac\Models\Role;
use Illuminate\Http\Request;

/**
 * Class RoleManager
 * @package App\Managers
 */
class RoleManager extends ManagerContract implements CRUDSInterface
{
    use FilterTrait;

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
     * @return RoleRepository
     */
    protected function roleRepository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function create()
    {
        $role = new RoleEntity();
        $role->description($this->requestInput('description'));
        return $role->merge($this->roleRepository()->create($role->toArray(true)))->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        $role = Role::getFromCacheId($id, function () use ($id) {
            return $this->roleRepository()->findById($id);
        });

        return RoleEntity::load($role)->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        $role = RoleEntity::load($this->requestInput());
        $this->roleRepository()->updateById($id, $role->toArray(true));
        return $this->read($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $role = $this->read($id);
        $this->roleRepository()->deleteById($id);
        return $role;
    }

    /**
     * @return array
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $roles = Role::getFromCacheTag($tag, function () {
            return $this->roleRepository()->findWhere($this->filterToApply());
        });

        return RoleEntity::toArrayMultiple($roles);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [
            'description' => 'required|max:255',
        ];
    }

    /**
     * @return array
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
