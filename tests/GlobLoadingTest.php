<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Spatie\Snapshots\MatchesSnapshots;

class GlobLoadingTest extends TestCase
{
    use MatchesSnapshots;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('breadcrumbs.files', [
            glob(__DIR__ . '/breadcrumbs/*.php')
        ]);
    }

    /** @test */
    public function it_loads_all_files_matched_by_glob(): void
    {
        $html = Breadcrumbs::render('multiple-file-test');

        $this->assertMatchesHtmlSnapshot($html);
    }
}
