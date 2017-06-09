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
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => Response::HTTP_UNAUTHORIZED,
                ]
            ];
        }

        if ($e instanceof HttpException) {
            $response = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getStatusCode(),
                ]
            ];
        }

        if ($e instanceof NotFoundHttpException) {
            $response = [
                'error' => [
                    'message' => $e->getMessage()?: 'Location not valid.',
                    'code' => Response::HTTP_NOT_FOUND,
                ]
            ];
        }

        if ($e instanceof ModelNotFoundException) {
            $response = [
                'error' => [
                    'message' => $e->getModel(),
                    'code' => Response::HTTP_NOT_FOUND,
                ]
            ];
        }

        if ($e instanceof ValidationException) {
            $response = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => ($e->getResponse())->original,
                ]
            ];
        }

        if ($e instanceof QueryException) {
            $response = [
                'error' => [
                    'message' => $e->errorInfo,
                    'code' => Response::HTTP_CONFLICT,
                ]
            ];
        }

        if ($e instanceof ProJetZionException) {
            $response = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
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

            return response()->json($response, $response['code']);
        }

//        dd('Exception not controller', get_class($e));

        return parent::render($request, $e);
    }
}
