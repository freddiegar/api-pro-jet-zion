<?php

namespace App\Http\Controllers;

use App\Managers\UserRoleManager;
use FreddieGar\Base\Constants\Action;
use Illuminate\Http\Response;

/**
 * Class UserRoleController
 * @package App\Http\Controllers
 */
class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-role.' . Action::INDEX, ['only' => 'index']);
        $this->middleware('permission:user-role.' . Action::STORE, ['only' => 'store']);
        $this->middleware('permission:user-role.' . Action::SHOW, ['only' => ['show', 'relationship']]);
        $this->middleware('permission:user-role.' . Action::UPDATE, ['only' => 'update']);
        $this->middleware('permission:user-role.' . Action::DESTROY, ['only' => 'destroy']);
    }

    /**
     * @return UserRoleManager
     */
    protected function manager()
    {
        return app(UserRoleManager::class);
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
