<?php

namespace Mandra;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Mandra\Decorators\Decorator;

/**
 * Class ServiceProvider
 *
 * @package Mandra
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindIf('mandra.decorators', function ($app) {
            $decorators = [];

            /** @var Decorator $decorator */
            foreach ($app['config']['mandra.decorators'] as $decorator) {
                $interfaces = class_implements($decorator);

                if (is_array($interfaces) && in_array(Decorator::class, $interfaces)) {
                    array_push($decorators, $app->make($decorator));
                }
            }

            return $decorators;
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/mandra.php' => config_path('mandra.php')
        ]);
    }
}