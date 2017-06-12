<?php

namespace App\Http\Controllers;

use App\Contracts\Commons\ManagerContract;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
abstract class Controller extends BaseController
{
    /**
     * @return ManagerContract
     */
    abstract protected function manager();
}
