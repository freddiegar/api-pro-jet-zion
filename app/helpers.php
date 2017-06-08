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
