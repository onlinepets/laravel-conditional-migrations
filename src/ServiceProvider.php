<?php

namespace Onlinepets\ConditionalMigrations;

use Illuminate\Support\ServiceProvider as LaravelProvider;

class ServiceProvider extends LaravelProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/conditional-migrations.php' => config_path('conditional-migrations.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/conditional-migrations.php', 'conditional-migrations');

        $this->app->extend('migrator', function ($migrator, $app) {
            return new Migrator($app['migration.repository'], $app['db'], $app['files'], $app['config']);
        });
    }
}
