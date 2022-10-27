<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Support;

trait ConfiguresForSingleFile
{
    protected function getEnvironmentSetup($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('breadcrumbs.files', [
            __DIR__ . '/../Fixtures/routes/breadcrumbs.php',
        ]);
    }
}
