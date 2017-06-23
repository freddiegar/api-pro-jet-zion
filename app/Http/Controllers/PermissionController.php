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
        $this->middleware('permission:permission.' . Action::SHOW, ['only' => 'show']);
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
        return responseJson($this->manager()->applyFilters()->show());
    }

    /**
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return responseJson($this->manager()->read($id));
    }
}
