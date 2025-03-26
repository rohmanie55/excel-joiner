<?php

namespace Mrohmani\ExcelJoiner;

use Illuminate\Support\ServiceProvider;
use Mrohmani\ExcelJoiner\Commands\ExcelJoinerHandler;

class ExcelJoinerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ExcelJoinerHandler::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/exceljoiner.php' => config_path('exceljoiner.php'),
            ], self::class);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exceljoiner.php', 'exceljoiner'
        );
    }
}