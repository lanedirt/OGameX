<?php

namespace OGame\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @inheritdoc
     */
    protected $dontReport = [
        //
    ];

    /**
     * @inheritdoc
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception|Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Exception|Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        return parent::render($request, $e);
    }
}
