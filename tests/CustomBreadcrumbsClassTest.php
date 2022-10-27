<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Support\UsesCustomBreadcrumbs;

uses(UsesCustomBreadcrumbs::class);

test('a custom breadcrumbs class can be used', function () {
    $breadcrumbs = Breadcrumbs::generate();

    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs[0])->toBe('custom-breadcrumbs-class');
});
