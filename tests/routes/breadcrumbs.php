<?php

declare(strict_types=1);

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for(
    'single-file-test',
    static function (Generator $trail) {
        $trail->push('Loaded');
    }
);
