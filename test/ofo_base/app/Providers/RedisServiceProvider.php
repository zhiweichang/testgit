<?php

namespace App\Providers;

use Illuminate\Redis\Database;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class RedisServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('redis', function ($app) {
            $config = $this->parseConfig($app['config']['database.redis'],
                config('database.redis_zk', [])
            );
            return new Database($config);
        });
    }

    protected function parseConfig($config, $zkConfig)
    {
        if (true !== array_get($zkConfig, 'use_zk')) {
            return $config;
        }

        if (empty($zkConfig['path'])) {
            throw new InvalidArgumentException("redis_zk [path] not configured.");
        }

        if (!is_array($zkConfig['path'])) {
            $zkConfig['path'] = [
                'default' => $zkConfig['path'],
            ];
        }
        $config = [
            'cluster' => false,
        ];
        foreach ($zkConfig['path'] as $key => $zkPath) {
            $config[$key] = $this->resolveZKConfig($zkPath);
        }
        return $config;
    }

    protected function resolveZKConfig($path)
    {
        $jsonConf = \Qconf::getConf($path);
        if (empty($jsonConf)) {
            throw new InvalidArgumentException("fetch redis zk conf failed.");
        }

        $dbConf = json_decode($jsonConf, true);

        return [
            'host'     => $dbConf['host'],
            'port'     => $dbConf['port'],
            'password' => $dbConf['password'] ?? null,
        ];
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['redis'];
    }
}