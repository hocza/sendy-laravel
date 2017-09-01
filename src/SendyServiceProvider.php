<?php

namespace Hocza\Sendy;

use Illuminate\Support\ServiceProvider;

/**
 * Class SendyServiceProvider
 *
 * @package Hocza\Sendy
 */
class SendyServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sendy.php' => config_path('sendy.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Hocza\Sendy\Sendy', function ($app) {
            return new Sendy($app['config']['sendy']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sendy'];
    }
}
