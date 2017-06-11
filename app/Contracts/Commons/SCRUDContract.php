<?php

namespace App\Contracts\Commons;

interface SCRUDContract
{
    /**
     * Show all models
     * @return array
     */
//    public function show();

    /**
     * Model create
     * @return array
     */
    public function create();

    /**
     * Read model specific
     * @param $id
     * @return array
     */
    public function read($id);

    /**
     * Update model specific
     * @return array
     */
//    public function update($data, $id);

    /**
     * Delete model specific
     * @return array
     */
//    public function delete($id);
}
