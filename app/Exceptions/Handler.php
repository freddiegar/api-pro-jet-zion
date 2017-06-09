<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if ($e instanceof UnauthorizedException) {
            $response = [
                'code' => Response::HTTP_UNAUTHORIZED,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];
        }

        if ($e instanceof HttpException) {
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
                    'message' => $e->getMessage() ?: 'Location not valid.',
                ]
            ];
        }

        if ($e instanceof ModelNotFoundException) {
            $response = [
                'code' => Response::HTTP_NOT_FOUND,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];
        }

        if ($e instanceof ValidationException) {
            $response = [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'error' => [
                    'message' => $e->getMessage(),
                    'errors' => ($e->getResponse())->original,
                ]
            ];
        }

        if ($e instanceof QueryException) {
            $response = [
                'code' => Response::HTTP_CONFLICT,
                'error' => [
                    'message' => $e->errorInfo,
                ]
            ];
        }

        if ($e instanceof ProJetZionException) {
            $response = [
                'code' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];
        }

        if (isset($response)) {
            if (isDevelopment()) {
                $response = array_merge($response['error'], [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'exception' => get_class($e),
//                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json($response['error'], $response['code']);
        }

        return parent::render($request, $e);
    }
}
