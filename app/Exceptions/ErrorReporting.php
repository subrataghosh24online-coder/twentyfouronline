<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\App;
use App\Facades\twentyfouronlineConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Spatie\LaravelIgnition\Facades\Flare;
use Throwable;
use twentyfouronline\Util\Git;

class ErrorReporting
{
    private const MAX_PROD_ERRORS = 4;
    private int $errorCount = 0;
    private ?bool $reportingEnabled = null;

    protected array $upgradable = [
        \twentyfouronline\Exceptions\FilePermissionsException::class,
        \twentyfouronline\Exceptions\DatabaseConnectException::class,
        \twentyfouronline\Exceptions\DuskUnsafeException::class,
        \twentyfouronline\Exceptions\UnserializableRouteCache::class,
        \twentyfouronline\Exceptions\MaximumExecutionTimeExceeded::class,
        \twentyfouronline\Exceptions\DatabaseInconsistentException::class,
    ];

    public function __construct(ExceptionHandler $exceptions, Application $app)
{
   app()->booted(function () {
    $this->adjustErrorHandlingForAppEnv(app()->environment());
});



    if (!method_exists($exceptions, 'dontReportDuplicates')) {
        \Log::warning('Custom exception handler does not support dontReportDuplicates()');
        return;
    }

    $exceptions->dontReportDuplicates();
    //$exceptions->throttle(fn (Throwable $e) => Limit::perMinute(twentyfouronlineConfig::get('reporting.throttle', 30)));
    $exceptions->reportable([$this, 'reportable']);
     $exceptions->reportable([$this, 'report']);

    $exceptions->render([$this, 'render']);

    Flare::determineVersionUsing(function () {
        return \twentyfouronline\Util\Version::VERSION;
    });
}



    public function reportable(Throwable $e): bool
    {
        \Log::critical('%RException: ' . get_class($e) . ' ' . $e->getMessage() . '%n @ %G' . $e->getFile() . ':' . $e->getLine() . '%n' . PHP_EOL . $e->getTraceAsString(), ['color' => true]);

        return false; // false = block default log message
    }

    public function report(Throwable $e): bool
    {
        if ($this->isReportingEnabled()) {
            Flare::report($e);
        }

        return true;
    }

    public function render(Throwable $exception, Request $request): ?Response
    {
        if (! config('app.debug')) {
            if ($exception instanceof \Illuminate\View\ViewException || $exception instanceof \Spatie\LaravelIgnition\Exceptions\ViewException) {
                $base = $exception->getPrevious();
            }

            foreach ($this->upgradable as $class) {
                if ($new = $class::upgrade($base ?? $exception)) {
                    return $new->render($request);
                }
            }
        }

        return null;
    }

    public function isReportingEnabled(): bool
    {
        if ($this->reportingEnabled !== null) {
            return $this->reportingEnabled;
        }

        if (! app()->bound('twentyfouronline-config')) {
            return false;
        }

        $this->reportingEnabled = false;

        if (twentyfouronlineConfig::get('reporting.error') !== true) {
            \Log::debug('Reporting disabled by user setting');
            return false;
        }

        if (! app()->isProduction()) {
            \Log::debug('Reporting disabled because app is not in production mode');
            return false;
        }

        $git = Git::make(180);
        if ($git->isAvailable()) {
            if (! Str::contains($git->remoteUrl(), [
                'git@github.com:twentyfouronline/twentyfouronline.git',
                'https://github.com/twentyfouronline/twentyfouronline.git'
            ])) {
                \Log::debug('Reporting disabled because twentyfouronline is not from the official repository');
                return false;
            }

            if ($git->hasChanges()) {
                \Log::debug('Reporting disabled because twentyfouronline is not from the official repository');
                return false;
            }

            if (! $git->isOfficialCommits()) {
                \Log::debug('Reporting disabled due to local modifications');
                return false;
            }
        }

        $this->reportingEnabled = true;

        return true;
    }

    private function adjustErrorHandlingForAppEnv(string $environment): void
    {
        if ($environment === 'testing' || ($environment !== 'production' && config('app.debug'))) {
            app()->booted(function () {
                config([
                    'logging.deprecations.channel' => 'deprecations_channel',
                    'logging.deprecations.trace' => true,
                ]);
            });

            return;
        }

        set_error_handler(function ($severity, $message, $file, $line) {
            if (self::isUndesirableTracePath($file)) {
                $message .= ' from ' . strstr($file, 'vendor') . ':' . $line;
                [$file, $line] = self::findFirstNonVendorFrame();
            }

            if ((error_reporting() & $severity) !== 0) {
                if ($this->errorCount++ < self::MAX_PROD_ERRORS) {
                    $max_errors = $this->errorCount == self::MAX_PROD_ERRORS ? ' (max reported errors reached)' : '';
                    error_log("\e[31mPHP Error($severity)\e[0m: $message in $file:$line$max_errors");
                }
            }

            if (($severity & (E_NOTICE | E_WARNING | E_USER_NOTICE | E_USER_WARNING | E_DEPRECATED)) !== 0) {
                return true;
            }

            return false;
        });
    }

    private static function findFirstNonVendorFrame(): array
    {
        foreach (debug_backtrace() as $trace) {
            if (isset($trace['file']) && self::isUndesirableTracePath($trace['file'])) {
                continue;
            }
            if (isset($trace['class']) && $trace['class'] === self::class) {
                continue;
            }

            return [$trace['file'], $trace['line']];
        }

        return ['', ''];
    }

    private static function isUndesirableTracePath(string $path): bool
    {
        return Str::contains($path, [
            '/vendor/',
            '/storage/framework/views/',
        ]);
    }
}
