<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Support;

use Rawilk\Breadcrumbs\Tests\Fixtures\CustomGenerator;

trait UsesCustomGenerator
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['breadcrumbs.generator_class'] = CustomGenerator::class;
    }
}
