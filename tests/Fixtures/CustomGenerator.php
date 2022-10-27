<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Fixtures;

use Illuminate\Support\Collection;
use Rawilk\Breadcrumbs\Contracts\Generator;

class CustomGenerator implements Generator
{
    public function generate(array $callbacks, array $before, string $name, array $params): Collection
    {
        return new Collection(['custom-generator']);
    }
}
