<?php

namespace Onlinepets\ConditionalMigrations\Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Onlinepets\ConditionalMigrations\Migrator;

class MigratorTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $db;

    /**
     * @var \Onlinepets\ConditionalMigrations\Migrator
     */
    protected $migrator;

    public function setUp()
    {
        $this->db = $db = new DB;

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $db->setAsGlobal();

        $container = new Container;
        $container->instance('db', $db->getDatabaseManager());

        Facade::setFacadeApplication($container);

        $this->migrator = new Migrator(
            $repository = new DatabaseMigrationRepository($db->getDatabaseManager(), 'migrations'),
            $db->getDatabaseManager(),
            new Filesystem,
            new Repository
        );

        if (! $repository->repositoryExists()) {
            $repository->createRepository();
        }
    }

    public function tearDown()
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
    }

    /** @test */
    public function it_handles_regular_migrations_normally()
    {
        $ran = $this->migrator->run([
            __DIR__.'/migrations/untimed',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('untimed_users'));

        $this->assertCount(1, $ran);
        $this->assertContains('untimed_users_table', $ran[0]);
    }

    /** @test */
    public function it_runs_a_migration_if_the_current_time_is_between_the_configured_times()
    {
        $this->migrator->run([
            __DIR__.'/migrations/untimed',
            __DIR__.'/migrations/timed',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('untimed_users'));
        $this->assertTrue($this->db->schema()->hasTable('timed_users_one'));
        $this->assertFalse($this->db->schema()->hasTable('timed_users_two'));
    }
}
