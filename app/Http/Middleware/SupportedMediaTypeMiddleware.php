<?php

namespace App\Http\Middleware;

use App\Contracts\Commons\ManagerContract;
use Closure;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class MediaTypeMiddleware
 * @package App\Http\Middleware
 */
class SupportedMediaTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (strtolower($request->headers->get('Content-Type')) !== ManagerContract::MEDIA_TYPE_SUPPORTED) {
            throw new UnsupportedMediaTypeHttpException(trans('login.error.unsopported_media_type', ['media_type' => ManagerContract::MEDIA_TYPE_SUPPORTED]));
        }

        return $next($request);
    }
}
