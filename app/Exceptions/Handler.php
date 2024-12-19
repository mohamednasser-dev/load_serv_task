<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            // Handle ModelNotFoundException
            if ($e instanceof ModelNotFoundException) {
                return msg( trans('lang.not_found'), Response::HTTP_NOT_FOUND);
            }

            // Handle NotFoundHttpException
            if ($e instanceof NotFoundHttpException) {
                return msg( trans('lang.not_found'), Response::HTTP_NOT_FOUND);
            }
            if ($e instanceof AuthorizationException) {
                return msg( trans('FORBIDDEN'), Response::HTTP_FORBIDDEN);
            }
            if ($e instanceof HttpException) {
                return msg( trans('FORBIDDEN'), Response::HTTP_FORBIDDEN);
            }


        }

        return parent::render($request, $e);
    }
}
