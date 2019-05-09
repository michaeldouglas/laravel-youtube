<?php

namespace Laravel\Youtube;

use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $config = realpath(__DIR__.'/../config/youtube.php');
        $this->publishes([$config => config_path('youtube.php')], 'config');
        $this->mergeConfigFrom($config, 'youtube');
    }

    public function register()
    {
        $this->app->singleton('youtube', function($app) {
            return new Youtube($app);
        });
    }
}