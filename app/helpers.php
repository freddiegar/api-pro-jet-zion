<?php

if (!function_exists('isDevelopment')) {
    /**
     * @return bool
     */
    function isDevelopment()
    {
        return in_array(env('APP_ENV'), ['local', 'testing']);
    }
}

if (!function_exists('hashing')) {
    /**
     * @param $string
     * @return string
     */
    function hashing($string)
    {
        return app('hash')->make($string);
    }
}

if (!function_exists('randomHashing')) {
    /**
     * @param int $length
     * @return string
     */
    function randomHashing($length = 64)
    {
        return hashing(str_random($length));
    }
}

if (!function_exists('pretty')) {
    /**
     * @param mixed $var
     * @return string
     */
    function pretty($var)
    {
        return print_r($var, 1);
    }
}

if (!function_exists('now')) {
    /**
     * @return int
     */
    function now()
    {
        return (new \Carbon\Carbon())->toDateTimeString();
    }
}
