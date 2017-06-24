<?php

namespace App\Http\Controllers;

use FreddieGar\Base\Contracts\Commons\ManagerContract;
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

    /**
     * @param int $id
     * @param string $relationship
     * @return array
     */
    public function relationship($id, $relationship)
    {
        return responseJson($this->manager()->relationship($id, $relationship));
    }
}
