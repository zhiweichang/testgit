<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class ZKDatabaseServiceProvider extends ServiceProvider
{

    const ZK_PATH_KEY = 'zk_path';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * if not read connection from QConf, stop it.
         */
        if ( ! config('database.read_connections_from_zk')) {
            return;
        }

        $connectionsZKConfig = config('database.connections');

        foreach ($connectionsZKConfig as $name => $config) {
            if (isset($config[self::ZK_PATH_KEY])) {
                $this->extendConnectionWithZk($name);
            }
        }
    }

    protected function extendConnectionWithZk($name)
    {
        $this->app->make('db')->extend($name, function ($config, $name) {
            return $this->zkMakeConnectionResolver($config, $name);
        });
    }

    protected function zkMakeConnectionResolver($config, $name)
    {
        if (empty($config[self::ZK_PATH_KEY])) {
            throw new InvalidArgumentException("connection [$name][zk_path] not configured.");
        }

        $jsonDBConf = \Qconf::getConf($config[self::ZK_PATH_KEY]);
        if (empty($jsonDBConf)) {
            throw new InvalidArgumentException("fetch db [$name] zk conf failed.");
        }

        $dbConf = json_decode($jsonDBConf, true);
        if (!isset($dbConf['host']) && is_int(key($dbConf))) {
            $dbConf = $dbConf[0];
        }
        if (!isset($dbConf['host'])) {
            \Log::error("the zk conf [$name] invalid: ", $dbConf);
            throw new InvalidArgumentException("the zk conf [$name] invalid.");
        }

        $config['host'] = $dbConf['host'];
        if (isset($dbConf['port'])) $config['port'] = $dbConf['port'];
        $config['username'] = $dbConf['user'];
        $config['password'] = $dbConf['password'];
        $config['database'] = $dbConf['database'];

        return $this->app->make('db.factory')->make($config, $name);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}