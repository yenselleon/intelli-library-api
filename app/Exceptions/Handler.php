<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use InvalidArgumentException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($exception);
        }

        return parent::render($request, $exception);
    }

    protected function isApiRequest($request)
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    protected function handleApiException(Exception $exception)
    {
        if ($exception instanceof TokenExpiredException) {
            return $this->errorResponse(401, 'Token has expired');
        }

        if ($exception instanceof TokenInvalidException) {
            return $this->errorResponse(401, 'Token is invalid');
        }

        if ($exception instanceof JWTException) {
            return $this->errorResponse(401, 'Token not provided');
        }

        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(401, 'Unauthenticated');
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'data' => $exception->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->errorResponse(404, 'Resource not found');
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(404, 'Endpoint not found');
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(405, 'Method not allowed for this endpoint');
        }

        if ($exception instanceof InvalidArgumentException && strpos($exception->getMessage(), 'Route') !== false) {
            return $this->errorResponse(401, 'Unauthenticated');
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getStatusCode(), $exception->getMessage() ?: 'HTTP Error');
        }

        $statusCode = 500;
        $message = config('app.debug') ? $exception->getMessage() : 'Internal Server Error';
        $debugData = config('app.debug') ? [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ] : null;

        return response()->json([
            'success' => false,
            'data' => $debugData,
            'message' => $message
        ], $statusCode);
    }

    protected function errorResponse($code, $message)
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message
        ], $code);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse(401, 'Unauthenticated');
    }
}
