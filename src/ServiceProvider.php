<?php

namespace Benrowe\Laravel\Config;

use Benrowe\Laravel\Config\Storage\File;
use Benrowe\Laravel\Config\Storage\Pdo;
use Benrowe\Laravel\Config\Storage\Redis;
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
            $storage = $this->selectStorage($this->app['config']);
            if ($storage === null) {
                $storage = [];
            }
            return new Config($storage);
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

    protected function selectStorage($config)
    {
        if (!$config->get('config.storage.enabled')) {
            return null;
        }
        $driver = $config->get('config.storage.driver', 'file');
        switch ($driver) {
            case 'pdo':
                $connection = $config->get('config.storage.connection');
                $table = $this->app['db']->getTablePrefix() . 'config';
                $pdo = $this->app['db']->connection($connection)->getPdo();
                $storage = new Pdo($pdo, $table);
                break;
            case 'redis':
                $connection = $config->get('config.storage.connection');
                $storage = new Redis($this->app['redis']->connection($connection));
                break;
            case 'custom':
                $class = $config->get('config.storage.provider');
                $storage = $this->app->make($class);
                break;
            case 'file':
            default:
                $path = $config->get('config.storage.path');
                $storage = new File($path);
                break;
        }
        return $storage;
    }
}
