<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Support;

trait ConfiguresForGlob
{
    protected function getEnvironmentSetup($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('breadcrumbs.files', [
            glob(__DIR__ . '/../Fixtures/breadcrumbs/*.php'),
        ]);
    }
}
