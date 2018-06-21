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
        $this->publishes([
            __DIR__.'/../config/eventsauce.php' => config_path('eventsauce.php'),
        ], 'config');
        
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/eventsauce.php', 'eventsauce');
    }
}
