<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Tests;

use ArtisanToolbox\Core\CoreServiceProvider;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InertiaServiceProvider::class,
            CoreServiceProvider::class,
        ];
    }
}
