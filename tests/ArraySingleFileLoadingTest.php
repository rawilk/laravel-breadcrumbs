<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Concerns\AssertsSnapshots;

class ArraySingleFileLoadingTest extends TestCase
{
    use AssertsSnapshots;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('breadcrumbs.files', [
            __DIR__ . '/routes/breadcrumbs.php',
        ]);
    }

    /** @test */
    public function it_loads_a_single_file_in_array_form(): void
    {
        $html = Breadcrumbs::render('single-file-test');

        $this->assertHtml($html);
    }
}
