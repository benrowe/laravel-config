<?php

/**
 * @author Ben Rowe <ben.rowe.83@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

namespace Benrowe\Laravel\Config;

use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for Config
 *
 * @package Benrowe\Laravel\Config;
 */
class ServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Boot the configuration component
     *
     * @return nil
     */
    public function boot()
    {
        # publish necessary files
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('config.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register an instance of the component
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('config', function () {
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
            'config'
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
