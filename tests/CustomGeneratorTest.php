<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Support\CustomGenerator;

class CustomGeneratorTest extends TestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['breadcrumbs.generator_class'] = CustomGenerator::class;
    }

    /** @test */
    public function a_custom_generator_can_be_used(): void
    {
        $breadcrumbs = Breadcrumbs::generate();

        self::assertSame('custom-generator', $breadcrumbs[0]);
    }
}
