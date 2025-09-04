<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\Handler;
use App\Exceptions\ErrorReporting;  

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    public function boot()
    {
        app()->booted(function () {
            new ErrorReporting(
                app(ExceptionHandler::class),
                app()
            );
        });
    }
}
