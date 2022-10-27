<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;
use Rawilk\Breadcrumbs\BreadcrumbsServiceProvider;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Sinnbeck\DomAssertions\DomAssertionsServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    protected function getPackageProviders($app): array
    {
        return [
            DomAssertionsServiceProvider::class,
            RayServiceProvider::class,
            BreadcrumbsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__ . '/Fixtures/resources/views']);
        $app['config']->set('breadcrumbs.view', 'breadcrumbs');
    }

    protected function getPackageAliases($app)
    {
        return [
            'Breadcrumbs' => Breadcrumbs::class,
        ];
    }
}
