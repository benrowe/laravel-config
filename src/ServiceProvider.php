<?php

namespace Benrowe\Laravel\Config;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Service Provider for Config
 *
 * @package Benrowe\Laravel\Config;
 */
class ServiceProvider extends BaseServiceProvider
{
    protected $defer = false;

    /**
     * @inheritdoc
     */
    public function boot()
    {
        # publish necessary files
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('config.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton('config-runtime', function () {
            return new Config();
        });
    }

    /**
     * Define the services this provider will build & provide
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Benrowe\Laravel\Config\Config',
            'config-runtime'
        ];
    }

    /**
     * Get the configuration destination path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return ;
    }
}
