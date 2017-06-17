<?php

namespace Freddiegar\Base\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class Handler
 * @package FreddieGar\Base\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $response = null;

        if ($e instanceof UnauthorizedException) {
            $response = [
                'code' => Response::HTTP_UNAUTHORIZED,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];
        }

        if ($e instanceof UnsupportedMediaTypeHttpException) {
            $response = [
                'code' => $e->getStatusCode(),
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        if ($e instanceof NotFoundHttpException) {
            $response = [
                'code' => Response::HTTP_NOT_FOUND,
                'error' => [
                    'message' => $e->getMessage() ?: trans('exceptions.not_found'),
                ]
            ];
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $response = [
                'code' => Response::HTTP_METHOD_NOT_ALLOWED,
                'error' => [
                    'message' => $e->getMessage() ?: trans('exceptions.method_not_allowed'),
                ]
            ];
        }

        if ($e instanceof ModelNotFoundException) {
            $response = [
                'code' => Response::HTTP_NOT_FOUND,
                'error' => [
                    'message' => trans('exceptions.model_not_found', ['model' => class_basename($e->getModel())]),
                ]
            ];
        }

        if ($e instanceof ValidationException) {
            $response = [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'error' => [
                    'message' => trans('exceptions.validation'),
                    'errors' => ($e->getResponse())->original,
                ]
            ];
        }

        if ($e instanceof QueryException) {
            $response = [
                'code' => Response::HTTP_CONFLICT,
                'error' => [
                    'message' => is_array($e->errorInfo) ? implode(' ', $e->errorInfo) : $e->errorInfo,
                ]
            ];
        }

        // @codeCoverageIgnoreStart
        if (!$response) {
            $response = [
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => [
                    'message' => trans('exceptions.internal_server_error'),
                ]
            ];
        }
        // @codeCoverageIgnoreEnd

        if (isDevelopment()) {
            $response['error'] = array_merge(
                ['status' => $response['code']],
                $response['error'],
                [
                    'description' => $e->getMessage(),
                    'exception' => get_class($e),
                    'from' => $e->getFile() . ':' . $e->getLine(),
                    'trace' => customizeTrace($e->getTrace()),
                ]
            );
        }

        return responseJson($response['error'], $response['code']);
    }
}
