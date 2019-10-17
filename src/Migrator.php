<?php

namespace Onlinepets\ConditionalMigrations;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Onlinepets\ConditionalMigrations\Contracts\ConditionalMigration;

class Migrator extends LaravelMigrator
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @param  \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Config\Repository  $config
     * @return void
     */
    public function __construct(
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files,
        Dispatcher $dispatcher,
        Repository $config
    ) {
        parent::__construct($repository, $resolver, $files, $dispatcher);

        $this->config = $config;
    }

    /**
     * @see \Illuminate\Database\Migrations\Migrator::runUp
     *
     * @param  string  $file
     * @param  int  $batch
     * @param  bool  $pretend
     * @return void
     */
    protected function runUp($file, $batch, $pretend)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
        }

        if ($migration instanceof ConditionalMigration && ! $this->shouldRunNow($migration)) {
            $this->note("<info>Skipped migrating</info>  {$name}");

            return;
        }

        $this->note("<comment>Migrating:</comment>  {$name}");

        $startTime = microtime(true);

        $this->runMigration($migration, 'up');

        $runTime = round(microtime(true) - $startTime, 2);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($name, $batch);

        $this->note("<info>Migrated:</info>  {$name} ({$runTime} seconds)");
    }

    /**
     * @param  \Onlinepets\ConditionalMigrations\Contracts\ConditionalMigration  $migration
     * @return bool
     */
    protected function shouldRunNow(ConditionalMigration $migration): bool
    {
        $alwaysRun = value($this->config->get('conditional-migrations.always_run'));

        return $alwaysRun || $migration->shouldRun();
    }
}
