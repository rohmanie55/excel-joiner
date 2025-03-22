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
        }
    }
}