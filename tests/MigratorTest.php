<?php

namespace Onlinepets\ConditionalMigrations\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->db = $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->setAsGlobal();

        $this->app->instance('db', $db->getDatabaseManager());

        $this->migrator = new Migrator(
            $repository = new DatabaseMigrationRepository($db->getDatabaseManager(), 'migrations'),
            $db->getDatabaseManager(),
            new Filesystem,
            new Dispatcher($this->app),
            $this->app->get('config')
        );

        if (! $repository->repositoryExists()) {
            $repository->createRepository();
        }
    }

    /** @test */
    public function it_handles_regular_migrations_normally()
    {
        $this->migrator->run([
            __DIR__.'/migrations/always',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('unconditional_users'));
    }

    /** @test */
    public function it_skips_migrations_that_should_not_run()
    {
        $this->migrator->run([
            __DIR__.'/migrations/always',
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('unconditional_users'));
        $this->assertTrue($this->db->schema()->hasTable('conditional_users_one'));
        $this->assertFalse($this->db->schema()->hasTable('conditional_users_two'));
    }

    /** @test */
    public function the_configuration_values_take_precendence_over_individual_configuration()
    {
        $this->app->get('config')->set('conditional-migrations.always_run', true);

        $this->migrator->run([
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('conditional_users_one'));
        $this->assertTrue($this->db->schema()->hasTable('conditional_users_two'));
    }
}
