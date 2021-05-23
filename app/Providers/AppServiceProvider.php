<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\ModelMakeCommand;

use App\Observers\DealObserver;
use App\Models\Deal;

use App\Observers\LoanSplitObserver;
use App\Models\LoanSplit;

use App\Observers\ReminderObserver;
use App\Models\Reminder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend('command.model.make', function ($command, $app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Deal::observe(DealObserver::class);
        LoanSplit::observe(LoanSplitObserver::class);
        Reminder::observe(ReminderObserver::class);
    }
}
