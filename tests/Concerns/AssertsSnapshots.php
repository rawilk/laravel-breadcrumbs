<?php

namespace Rawilk\Breadcrumbs\Tests\Concerns;

use Spatie\Snapshots\MatchesSnapshots;

trait AssertsSnapshots
{
    use MatchesSnapshots;

    protected function assertHtml(string $html): void
    {
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', ">\n<", $html);

        $this->assertMatchesXmlSnapshot($html);
    }
}
