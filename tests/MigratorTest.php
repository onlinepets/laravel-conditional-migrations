<?php

namespace Onlinepets\ConditionalMigrations\Tests;

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
        parent::setUp();

        $this->db = $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->setAsGlobal();

        $container = new Container;
        $container->instance('db', $db->getDatabaseManager());

        Facade::setFacadeApplication($container);

        $this->migrator = new Migrator(
            $repository = new DatabaseMigrationRepository($db->getDatabaseManager(), 'migrations'),
            $db->getDatabaseManager(),
            new Filesystem,
            $this->app->get('config')
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
            __DIR__.'/migrations/always',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('untimed_users'));

        $this->assertCount(1, $ran);
        $this->assertContains('untimed_users_table', $ran[0]);
    }

    /** @test */
    public function it_skips_migrations_that_should_not_run()
    {
        $this->migrator->run([
            __DIR__.'/migrations/always',
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('untimed_users'));
        $this->assertTrue($this->db->schema()->hasTable('timed_users_one'));
        $this->assertFalse($this->db->schema()->hasTable('timed_users_two'));
    }

    /** @test */
    public function the_configuration_values_take_precendence_over_individual_configuration()
    {
        $this->app->get('config')->set('conditional-migrations.always_run', true);

        $this->migrator->run([
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('timed_users_one'));
        $this->assertTrue($this->db->schema()->hasTable('timed_users_two'));
    }
}
