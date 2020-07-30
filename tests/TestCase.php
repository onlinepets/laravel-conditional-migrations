<?php

namespace MLL\ConditionalMigrations\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use MLL\ConditionalMigrations\ServiceProvider;

abstract class TestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return ServiceProvider::class;
    }
}
