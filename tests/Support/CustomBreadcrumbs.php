<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Support;

use Illuminate\Support\Collection;
use Rawilk\Breadcrumbs\Breadcrumbs;

class CustomBreadcrumbs extends Breadcrumbs
{
    public function generate(string $name = null, ...$params): Collection
    {
        return new Collection(['custom-manager']);
    }
}
