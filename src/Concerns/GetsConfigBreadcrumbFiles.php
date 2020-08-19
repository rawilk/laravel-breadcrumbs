<?php

namespace Rawilk\Breadcrumbs\Concerns;

use Illuminate\Support\Arr;

trait GetsConfigBreadcrumbFiles
{
    protected function getBreadcrumbFiles(): array
    {
        $files = config('breadcrumbs.files');

        if (! is_array($files)) {
            $files = [$files];
        }

        return Arr::flatten($files);
    }
}
