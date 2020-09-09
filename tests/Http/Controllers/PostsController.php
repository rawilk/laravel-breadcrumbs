<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Http\Controllers;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Models\Post;

class PostsController
{
    public function edit(Post $post)
    {
        return Breadcrumbs::render();
    }
}
