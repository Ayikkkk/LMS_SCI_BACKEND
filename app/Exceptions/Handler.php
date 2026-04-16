<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // Validation errors → 422 JSON
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Model not found → 404 JSON
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }
        });

        // Route not found → 404 JSON
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan',
                ], 404);
            }
        });

        // Method not allowed → 405 JSON
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method tidak diizinkan',
                ], 405);
            }
        });

        // Authorization → 403 JSON
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak',
                ], 403);
            }
        });

        // Unhandled server errors → 500 JSON + log
        $this->reportable(function (\Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid, silakan login kembali',
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
