<?php

namespace Onlinepets\ConditionalMigrations;

use Illuminate\Support\ServiceProvider as LaravelProvider;

class ServiceProvider extends LaravelProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../src/conditional-migrations.php' => config_path('conditional-migrations.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../src/conditional-migrations.php', 'conditional-migrations');

        $this->app->extend('migrator', static function ($migrator, $app): Migrator {
            return new Migrator(
                $app['migration.repository'],
                $app['db'],
                $app['files'],
                $app['events'],
                $app['config']
            );
        });
    }
}
