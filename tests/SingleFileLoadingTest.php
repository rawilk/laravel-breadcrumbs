<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Spatie\Snapshots\MatchesSnapshots;

class SingleFileLoadingTest extends TestCase
{
    use MatchesSnapshots;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('breadcrumbs.files', __DIR__ . '/routes/breadcrumbs.php');
    }

    /** @test */
    public function it_loads_a_single_file(): void
    {
        $html = Breadcrumbs::render('single-file-test');

        $this->assertMatchesHtmlSnapshot($html);
    }
}
