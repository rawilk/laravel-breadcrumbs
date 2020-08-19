<?php

namespace Rawilk\Breadcrumbs\Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Rawilk\Breadcrumbs\Tests\Models\Post;

trait NeedsDatabase
{
    protected function setUpDatabase($app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('posts', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        Post::create(['id' => 1]);
    }
}
