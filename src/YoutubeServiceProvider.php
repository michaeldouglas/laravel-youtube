<?php

namespace Laravel\Youtube;

use Illuminate\Support\ServiceProvider;

class YoutubeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $config = realpath(__DIR__ . '/complements/config/youtube.php');

        $this->publishes([$config => config_path('youtube.php')], 'config');

        $this->mergeConfigFrom($config, 'youtube');

        $this->setComplementsRouter();

        $this->publishesLibrarys(__DIR__ . '/complements/controllers/', 'app/Http/Controllers', null);
    }

    private function setComplementsRouter()
    {
        if ($this->app->config->get('youtube.routes.enabled')) {
            include __DIR__ . '/complements/routes/web.php';
        }
    }

    private function publishesLibrarys($path, $pathLaravel, $groups)
    {
        if (!is_null($groups)) {
            $this->publishes([$path => $pathLaravel], $groups);
        } else {
            $this->publishes([$path => $pathLaravel]);
        }
    }

    public function register()
    {
        $this->app->singleton('youtube', function ($app) {
            return new Youtube($app, new \Google_Client);
        });
    }
}