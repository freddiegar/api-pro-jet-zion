<?php

namespace App\Http\Controllers;

use App\Managers\PermissionManager;
use FreddieGar\Base\Constants\Action;

/**
 * Class PermissionController
 * @package App\Http\Controllers
 */
class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permission.' . Action::INDEX, ['only' => 'index']);
        $this->middleware('permission:permission.' . Action::STORE, ['only' => 'store']);
        $this->middleware('permission:permission.' . Action::SHOW, ['only' => ['show', 'relationship']]);
        $this->middleware('permission:permission.' . Action::UPDATE, ['only' => 'update']);
        $this->middleware('permission:permission.' . Action::DESTROY, ['only' => 'destroy']);
    }

    /**
     * @return PermissionManager
     */
    protected function manager()
    {
        return app(PermissionManager::class);
    }

    /**
     * @return array
     */
    public function index()
    {
        return responseJsonApi($this->manager()->applyFilters()->show());
    }

    /**
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return responseJsonApi($this->manager()->read($id));
    }
}
