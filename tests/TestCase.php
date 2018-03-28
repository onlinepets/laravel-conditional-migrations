<?php

namespace Onlinepets\TimedMigrations\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Onlinepets\TimedMigrations\ServiceProvider;

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
