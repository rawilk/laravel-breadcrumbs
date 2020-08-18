<?php

namespace Rawilk\Breadcrumbs\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rawilk\Breadcrumbs\BreadcrumbsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BreadcrumbsServiceProvider::class,
        ];
    }
}
