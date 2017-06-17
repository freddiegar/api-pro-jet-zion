<?php

namespace FreddieGar\Base\Contracts\Interfaces;

/**
 * Interface CacheControlInterface
 * @package FreddieGar\Base\Constants\Interfaces
 */
interface CacheControlInterface
{
    /**
     * @param int $id
     * @return string
     */
    static public function label($id);

    /**
     * @return string
     */
    static public function tag();

    /**
     * @param $tag
     */
    static public function setTag($tag);

    /**
     * @param $id
     * @param $value
     */
    static public function setByLabel($id, $value);

    /**
     * @param $name
     * @param $value
     */
    static public function setByTag($name, $value);

    /**
     * @param $id
     * @return bool
     */
    static public function existLabel($id);

    /**
     * @param $id
     * @return mixed
     */
    static public function getByLabel($id);

    /**
     * @param $name
     * @return mixed
     */
    static public function getByTag($name);

    /**
     * @param $id
     */
    static public function unsetByLabel($id);

    /**
     * Eraser cache by tag
     */
    static public function unsetByTag();

    /**
     * Enable cache
     */
    static public function enableCache();

    /**
     * Enable cache
     */
    static public function disableCache();
}
