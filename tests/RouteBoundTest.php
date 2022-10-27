<?php

declare(strict_types=1);

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Fixtures\app\Http\Controllers\PostsController;
use Rawilk\Breadcrumbs\Tests\Fixtures\app\Models\Post;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->domain = 'http://localhost';

    createPostsTable();
    createPost();
});

afterEach(function () {
    dropPostsTable();
});

it('renders route bound breadcrumbs', function () {
    defineBreadcrumbs();

    Route::get('/', fn () => '')->name('home');

    // Home > [Post]
    Route::get('/post/{id}', fn () => Breadcrumbs::render())->name('post');

    get('/post/1')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Post 1')
                        ->doesntContain('a');
                });
        });
});

it('generates route bound breadcrumbs', function () {
    defineBreadcrumbs();

    Route::get('/', fn () => '')->name('home');

    // Home > [Post]
    Route::get('/post/{id}', fn () => Breadcrumbs::generate())->name('post');

    $breadcrumbs = collect(json_decode(get('/post/1')->getContent(), true))
        ->map(fn (array $breadcrumb) => (object) $breadcrumb);

    expect($breadcrumbs)->count()->toBe(2)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Post 1')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/post/1");
});

it('can render route bound breadcrumbs with custom views', function () {
    defineBreadcrumbs();

    Route::get('/', fn () => '')->name('home');

    // Home > [Post]
    Route::get('/post/{id}', fn () => Breadcrumbs::view('breadcrumbs2'))->name('post');

    get('/post/1')
        ->assertElementExists('ul.custom-view', function (AssertElement $ul) {
            $ul->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Post 1')
                        ->doesntContain('a');
                });
        });
});

it('can check if a route bound breadcrumb exists', function () {
    $exists = false;

    Breadcrumbs::for('exists', fn () => '');
    Route::get('/exists', function () use (&$exists) {
        $exists = Breadcrumbs::exists();
    })->name('exists');

    get('/exists');
    expect($exists)->toBeTrue();

    $otherExists = true;
    Route::get('/not-exists', function () use (&$otherExists) {
        $otherExists = Breadcrumbs::exists();
    })->name('not-exists');

    get('/not-exists');
    expect($otherExists)->toBeFalse();

    // Unnamed routes should also be able to be checked and not trigger an exception.
    $unnamedExists = true;

    Route::get('/unnamed', function () use (&$unnamedExists) {
        $unnamedExists = Breadcrumbs::exists();
    });

    get('/unnamed');
    expect($unnamedExists)->toBeFalse();
});

it('can handle implicit model binding', function () {
    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));
    Breadcrumbs::for('post', fn (Generator $trail, $post) => $trail->parent('home')->push($post->title, route('post', $post)));

    Route::get('/', fn () => '')->name('home');
    Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::render())->middleware(SubstituteBindings::class)->name('post');

    get('/post/1')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Post 1')
                        ->doesntContain('a');
                });
        });
});

it('can handle explicit model binding', function () {
    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));
    Breadcrumbs::for('post', fn (Generator $trail, Post $post) => $trail->parent('home')->push($post->title, route('post', $post)));

    Route::get('/', fn () => '')->name('home');
    Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::render())->name('post')->middleware(SubstituteBindings::class);

    get('/post/1')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Post 1')
                        ->doesntContain('a');
                });
        });
});

it('can handle resourceful controllers', function () {
    Route::middleware(SubstituteBindings::class)->resource('post', PostsController::class);

    Breadcrumbs::for('post.index', fn (Generator $trail) => $trail->push('Posts', route('post.index')));

    // Posts > Upload Post
    Breadcrumbs::for('post.create', fn (Generator $trail) => $trail->parent('post.index')->push('New Post', route('post.create')));

    // Posts > [Post Name]
    Breadcrumbs::for('post.show', fn (Generator $trail, Post $post) => $trail->parent('post.index')->push($post->title, route('post.show', $post->id)));

    // Posts > [Post Name] > Edit Post
    Breadcrumbs::for('post.edit', fn (Generator $trail, Post $post) => $trail->parent('post.show', $post)->push('Edit Post', route('post.edit', $post->id)));

    get('/post/1/edit')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 3)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/post",
                        'text' => 'Posts',
                    ]);
                })
                ->find('li:nth-of-type(2)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/post/1",
                        'text' => 'Post 1',
                    ]);
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Edit Post')
                        ->doesntContain('a');
                });
        });
});

// Helpers...
function defineBreadcrumbs(): void
{
    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

    Breadcrumbs::for('post', function (Generator $trail, $id) {
        $post = Post::findOrFail($id);

        $trail->parent('home')->push($post->title, route('post', $post));
    });
}
