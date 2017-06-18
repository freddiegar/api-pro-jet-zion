<?php

namespace FreddieGar\Base\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * Trait CacheControlTrait
 * @package FreddieGar\Base\Traits
 */
trait CacheControlTrait
{
    /**
     * @var bool
     */
    static private $ENABLED_CACHE = true;

    /**
     * @var string
     */
    static private $TAG = null;

    /**
     * @param int $id
     * @return string
     */
    final static public function label($id)
    {
        return sprintf('%s:%d', static::tag(), $id);
    }

    /**
     * @return string
     */
    final static public function tag()
    {
        return static::$TAG ?: get_called_class();
    }

    /**
     * @param $id
     * @return bool
     */
    static public function existLabel($id)
    {
        return self::isEnableCache() && Cache::has(self::label($id));
    }

    /**
     * @param $name
     * @return bool
     */
    static public function existTag($name)
    {
        return self::isEnableCache() && Cache::tags(self::tag())->has($name);
    }

    /**
     * @param $tag
     */
    final static public function setTag($tag)
    {
        static::$TAG = $tag;
    }

    /**
     * @param $id
     * @param $value
     */
    final static public function setByLabel($id, $value)
    {
        Cache::forever(self::label($id), $value);
    }

    /**
     * @param $name
     * @param $value
     */
    final static public function setByTag($name, $value)
    {
        Cache::tags(self::tag())->forever($name, $value);
    }

    /**
     * @param $id
     * @return mixed
     */
    final static public function getByLabel($id)
    {
        return Cache::get(self::label($id));
    }

    /**
     * @param $name
     * @return mixed
     */
    final static public function getByTag($name)
    {
        return Cache::tags(self::tag())->get($name);
    }

    /**
     * @param $id
     */
    final static public function unsetByLabel($id)
    {
        Cache::forget(self::label($id));
    }

    /**
     * Eraser cacge by tag
     */
    final static public function unsetByTag()
    {
        Cache::tags(self::tag())->flush();
    }

    /**
     * Enable cache
     */
    final static public function enableCache()
    {
        self::rebootCacheControlTrait();
        static::$ENABLED_CACHE = true;
    }

    /**
     * Enable cache
     */
    final static public function disableCache()
    {
        self::rebootCacheControlTrait();
        static::$ENABLED_CACHE = false;
    }

    /**
     * @return bool
     */
    final static private function isEnableCache()
    {
        return static::$ENABLED_CACHE;
    }

    /**
     *
     */
    final static private function rebootCacheControlTrait()
    {
        static::flushEventListeners();
        static::clearBootedModels();
    }
}
