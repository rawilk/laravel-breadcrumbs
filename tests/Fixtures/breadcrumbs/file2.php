<?php

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for(
    'multiple-file-test-parent',
    fn (Generator $trail) => $trail->push('Parent')
);
