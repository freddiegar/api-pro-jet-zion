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
    static public function hasInCacheId($id)
    {
        return Cache::has(self::label($id));
    }

    /**
     * @param $tag
     * @return bool
     */
    static public function hasInCacheTag($tag)
    {
        return Cache::tags(self::tag())->has($tag);
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
     * @return mixed
     */
    final static public function setCacheById($id, $value)
    {
        Cache::forever(self::label($id), $value);
        return static::getCacheById($id);
    }

    /**
     * @param $tag
     * @param $value
     * @return mixed
     */
    final static public function setCacheByTag($tag, $value)
    {
        Cache::tags(self::tag())->forever($tag, $value);
        return static::getCacheByTag($tag);
    }

    /**
     * @param $id
     * @return mixed
     */
    final static public function getCacheById($id)
    {
        return Cache::get(self::label($id));
    }

    /**
     * @param $tag
     * @return mixed
     */
    final static public function getCacheByTag($tag)
    {
        return Cache::tags(self::tag())->get($tag);
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
        static::$ENABLED_CACHE = true;
        self::rebootCacheControlTrait();
    }

    /**
     * Enable cache
     */
    final static public function disableCache()
    {
        static::$ENABLED_CACHE = false;
        self::rebootCacheControlTrait();
    }

    /**
     * @return bool
     */
    final static protected function hasEnableCache()
    {
        return static::$ENABLED_CACHE === true;
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
