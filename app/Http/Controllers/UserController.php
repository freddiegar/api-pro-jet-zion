<?php

namespace App\Http\Controllers;

use App\Managers\UserManager;
use FreddieGar\Base\Constants\Action;
use Illuminate\Http\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.' . Action::INDEX, ['only' => 'index']);
        $this->middleware('permission:user.' . Action::STORE, ['only' => 'store']);
        $this->middleware('permission:user.' . Action::SHOW, ['only' => ['show', 'relationship']]);
        $this->middleware('permission:user.' . Action::UPDATE, ['only' => 'update']);
        $this->middleware('permission:user.' . Action::DESTROY, ['only' => 'destroy']);
    }

    /**
     * @return UserManager
     */
    protected function manager()
    {
        return app(UserManager::class);
    }

    /**
     * @return array
     */
    public function index()
    {
        return responseJsonApi($this->manager()->applyFilters()->show());
    }

    /**
     * @return array
     */
    public function store()
    {
        return responseJsonApi($this->manager()->requestValidate()->create(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return responseJsonApi($this->manager()->read($id));
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        return responseJsonApi($this->manager()->requestValidate()->update($id));
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        return responseJsonApi($this->manager()->delete($id));
    }
}
