<?php

namespace FreddieGar\Rbac\Middlewares;

use App\Contracts\Repositories\UserRepository;
use Closure;
use Exception;
use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class VerifyPermissionMiddleware
 * @package FreddieGar\Rbac\Middlewares
 */
class VerifyPermissionMiddleware
{
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     * @param UserRepository $userRepository
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(Guard $auth, UserRepository $userRepository, PermissionRepository $permissionRepository)
    {
        $this->auth = $auth;
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param string $slug
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next, $slug)
    {
        if ($this->auth->check() && $this->userRepository->can($slug)) {
            return $next($request);
        }

        $permission = $this->permissionRepository->findBySlug($slug);
        throw new Exception(trans('exceptions.not_permission', ['description' => $permission['description']]));
    }
}
