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
        return '';
    }

    protected function selectStorage(Config $config)
    {
        $storage = false;
        if ($config->get('config.storage.enabled')) {
            $driver = $config->get('config.storage.driver', 'file');
            switch ($driver) {
                case 'pdo':
                    $connection = $config->get('config.storage.connection');
                    $table = $this->app['db']->getTablePrefix() . 'config';
                    $pdo = $this->app['db']->connection($connection)->getPdo();
                    $storage = new Benrowe\Laravel\Config\Storage\Pdo($pdo, $table);
                    break;
                case 'redis':
                    $connection = $config->get('config.storage.connection');
                    $storage = new RedisStorage($this->app['redis']->connection($connection));
                    break;
                case 'custom':
                    $class = $config->get('config.storage.provider');
                    $storage = $this->app->make($class);
                    break;
                case 'file':
                default:
                    $path = $config->get('config.storage.path');
                    $storage = new Benrowe\Laravel\Config\Storage\File($path);
                    break;
            }
        }
        return $storage;
    }
}
