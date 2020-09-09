<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Support\CustomBreadcrumbs;

class CustomBreadcrumbsClassTest extends TestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['breadcrumbs.breadcrumbs_class'] = CustomBreadcrumbs::class;
    }

    /** @test */
    public function a_custom_breadcrumbs_class_can_be_used(): void
    {
        $breadcrumbs = Breadcrumbs::generate();

        self::assertSame('custom-manager', $breadcrumbs[0]);
    }
}
