<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Rawilk\Breadcrumbs\Tests\Fixtures\app\Models\Post;
use Rawilk\Breadcrumbs\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Helpers...
function createPostsTable(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->increments('id');
        $table->string('title');
    });
}

function dropPostsTable(): void
{
    Schema::drop('posts');
}

function createPost(?array $attributes = null): void
{
    $attributes = $attributes ?: ['title' => 'Post 1'];

    Post::create($attributes);
}
