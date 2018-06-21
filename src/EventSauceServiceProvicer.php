<?php

namespace JSellis\EloquentMessageRepository;

use Illuminate\Support\ServiceProvider;

class EventSauceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }
    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
