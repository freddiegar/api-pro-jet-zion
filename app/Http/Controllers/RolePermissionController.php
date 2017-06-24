<?php

namespace App\Http\Controllers;

use App\Managers\RolePermissionManager;
use FreddieGar\Base\Constants\Action;
use Illuminate\Http\Response;

/**
 * Class RolePermissionController
 * @package App\Http\Controllers
 */
class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-permission.' . Action::INDEX, ['only' => 'index']);
        $this->middleware('permission:role-permission.' . Action::STORE, ['only' => 'store']);
        $this->middleware('permission:role-permission.' . Action::SHOW, ['only' => ['show', 'relationship']]);
        $this->middleware('permission:role-permission.' . Action::UPDATE, ['only' => 'update']);
        $this->middleware('permission:role-permission.' . Action::DESTROY, ['only' => 'destroy']);
    }

    /**
     * @return RolePermissionManager
     */
    protected function manager()
    {
        return app(RolePermissionManager::class);
    }

    /**
     * @return array
     */
    public function index()
    {
        return responseJson($this->manager()->applyFilters()->show());
    }

    /**
     * @return array
     */
    public function store()
    {
        return responseJson($this->manager()->requestValidate()->create(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return responseJson($this->manager()->read($id));
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        return responseJson($this->manager()->requestValidate()->update($id));
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        return responseJson($this->manager()->delete($id));
    }
}
