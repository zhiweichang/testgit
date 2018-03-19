<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //先load配置，下面register过程都有用到
        $this->loadCustomConfiguration();

        $this->registerLogger();
        $this->registerRequest();
        $this->registerRabbit();
        $this->registerZKDatabaseServiceProvider();
//        $this->registerRedisProvider();
    }

    public function registerLogger()
    {
        $this->app->register(\Ofo\Utils\Log\Providers\LumenLoggerServiceProvider::class);

        if(!class_exists('OfoLog')) {
            class_alias('Ofo\Utils\Log\Facades\OfoLog', 'OfoLog');
        }
    }

    public function registerRequest()
    {
        $this->app->register(\Ofo\Utils\Request\Providers\LumenRequestProvider::class);

        if(!class_exists('OfoRequest')) {
            class_alias('Ofo\Utils\Request\Facades\OfoRequest', 'OfoRequest');
        }
    }

    public function registerRabbit()
    {
        $this->app->register(\Ofo\Utils\Rabbit\Providers\LumenRabbitProvider::class);

        if (!class_exists('OfoRabbit')) {
            class_alias('Ofo\Utils\Rabbit\Facades\OfoRabbit', 'OfoRabbit');
        }
    }

    public function registerZKDatabaseServiceProvider()
    {
        $this->app->register(ZKDatabaseServiceProvider::class);
    }

    public function registerRedisProvider()
    {
        $this->app->register(RedisServiceProvider::class);

        if(!class_exists('PRedis')) {
            class_alias('Illuminate\Support\Facades\Redis', 'PRedis');
        }
    }

    /**
     * Boot the application services.
     */
    public function boot()
    {
    }

    protected function loadCustomConfiguration()
    {
        foreach ($this->getConfigFiles() as $key => $path) {
            $this->app->make('config')->set($key, require $path);
        }
    }

    /**
     * Get the custom config files.
     * @return array
     */
    protected function getConfigFiles()
    {
        $configurationFiles = [];
        $configPath = $this->app->basePath('config');
        $files = glob($configPath . '/*.php', GLOB_BRACE);
        foreach ($files as $file)
        {
            $name = strtolower(pathinfo($file, PATHINFO_FILENAME));
            $configurationFiles[$name] = $file;
        }
        return $configurationFiles;
    }
}