<?php

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for(
    'single-file-test',
    static function (Generator $trail) {
        $trail->push('Loaded');
    }
);
