<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rawilk\Breadcrumbs\BreadcrumbsServiceProvider;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BreadcrumbsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__ . '/resources/views']);
        $app['config']->set('breadcrumbs.view', 'breadcrumbs');
    }

    protected function getPackageAliases($app)
    {
        return [
            'Breadcrumbs' => Breadcrumbs::class,
        ];
    }
}
