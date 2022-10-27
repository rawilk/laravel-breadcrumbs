<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Support;

use Rawilk\Breadcrumbs\Tests\Fixtures\CustomBreadcrumbs;

trait UsesCustomBreadcrumbs
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['breadcrumbs.breadcrumbs_class'] = CustomBreadcrumbs::class;
    }
}
