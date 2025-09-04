<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\ErrorReporting;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
    })
    ->create();

$app->usePublicPath(realpath(base_path('html')));

// Register your exception handler
$app->singleton(ExceptionHandler::class, Handler::class);

// Custom error reporting setup
$app->booted(function () use ($app) {
    new ErrorReporting(
        $app->make(ExceptionHandler::class),
        $app
    );
});

return $app;
