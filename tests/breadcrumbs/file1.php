<?php

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for(
    'multiple-file-test',
    fn (Generator $trail) => $trail->parent('multiple-file-test-parent')->push('Loaded')
);
