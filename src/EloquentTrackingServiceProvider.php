<?php

namespace Gtk\EloquentTracking;

use Illuminate\Support\ServiceProvider;

class EloquentTrackingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'tracking-migrations');
    }
}
