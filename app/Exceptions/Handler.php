<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\ApiException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Server Error';
        $data = [];

        if ($exception instanceof ApiException) {
            $statusCode = $exception->getCode() ?: 500;
            $message = $exception->getMessage();
        }
        elseif ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation Error';
            $data = $exception->errors();
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message =  $exception->getMessage()? $exception->getMessage(): 'Unauthenticated';
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Resource Not Found';
        } else {
            $message = $exception->getMessage() ?: $message;
        }

        return response()->json([
            'status' => [
                'code' => $statusCode,
                'message' => $message,
            ],
            'data' => $data
        ], $statusCode);
    }

    // protected function unauthenticated($request, AuthenticationException $exception)
    // {
    //     return response()->json([
    //         'status' => [
    //             'code' => 401,
    //             'message' => 'Unauthenticated'
    //         ],
    //         'data' => []
    //     ], 401);
    // }


}
