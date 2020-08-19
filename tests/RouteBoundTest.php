<?php

namespace Rawilk\Breadcrumbs\Tests;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Concerns\NeedsDatabase;
use Rawilk\Breadcrumbs\Tests\Http\Controllers\PostsController;
use Rawilk\Breadcrumbs\Tests\Models\Post;
use Spatie\Snapshots\MatchesSnapshots;

class RouteBoundTest extends TestCase
{
    use MatchesSnapshots, NeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /** @test */
    public function it_renders_route_bound_breadcrumbs(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        // Home > [Post]
        Route::get('/post/{id}', fn () => Breadcrumbs::render())->name('post');

        Breadcrumbs::for('post', function (Generator $trail, $id) {
            $post = Post::findOrFail($id);

            $trail->parent('home')->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_generates_route_bound_breadcrumbs(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        $breadcrumbs = null;

        // Home > [Post]
        Route::get('/post/{id}', function () use (&$breadcrumbs) {
            $breadcrumbs = Breadcrumbs::generate();
        })->name('post');

        Breadcrumbs::for('post', function (Generator $trail, $id) {
            $post = Post::findOrFail($id);

            $trail->parent('home')->push($post->title, route('post', $post));
        });

        $this->get('/post/1');

        self::assertCount(2, $breadcrumbs);

        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);

        self::assertSame('Post 1', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/post/1', $breadcrumbs[1]->url);
    }

    /** @test */
    public function it_can_render_route_bound_breadcrumbs_with_custom_views(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        // Home > [Post]
        Route::get('/post/{id}', fn () => Breadcrumbs::view('breadcrumbs2'))->name('post');

        Breadcrumbs::for('post', function (Generator $trail, $id) {
            $post = Post::findOrFail($id);

            $trail->parent('home')->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_check_if_a_route_bound_breadcrumb_exists(): void
    {
        $exists1 = false;

        Breadcrumbs::for('exists', function () {
        });

        Route::get('/exists', function () use (&$exists1) {
            $exists1 = Breadcrumbs::exists();
        })->name('exists');

        $this->get('/exists');
        self::assertTrue($exists1);

        $exists2 = true;

        Route::get('/not-exists', function () use (&$exists2) {
            $exists2 = Breadcrumbs::exists();
        })->name('not-exists');

        $this->get('not-exists');
        self::assertFalse($exists2);

        // Unnamed routes should also be able to be checked and not trigger an exception.
        $exists3 = true;

        Route::get('/unnamed', function () use (&$exists3) {
            $exists3 = Breadcrumbs::exists();
        });

        $this->get('/unnamed');
        self::assertFalse($exists3);
    }

    /** @test */
    public function the_404_page_template_can_have_breadcrumbs(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        Breadcrumbs::for('errors.404', fn (Generator $trail) => $trail->parent('home')->push('Not Found'));

        $html = $this->withExceptionHandling()->get('/does-not-exist')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_handle_explicit_model_binding(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        // Home > [Post]
        Route::model('post', Post::class);
        Route::get('/post/{post}', fn () => Breadcrumbs::render())->name('post')->middleware(SubstituteBindings::class);

        Breadcrumbs::for('post', fn (Generator $trail, Post $post) => $trail->parent('home')->push($post->title, route('post', $post)));

        $html = $this->get('/post/1')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_handle_implicit_model_binding(): void
    {
        Route::get('/', function () {
        })->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        // Home > [Post]
        Route::get('/post/{post}', function (Post $post) {
            return Breadcrumbs::render();
        })->name('post')->middleware(SubstituteBindings::class);

        Breadcrumbs::for('post', fn (Generator $trail, $post) => $trail->parent('home')->push($post->title, route('post', $post)));

        $html = $this->get('/post/1')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_handle_resourceful_controllers(): void
    {
        Route::middleware(SubstituteBindings::class)->resource('post', PostsController::class);

        Breadcrumbs::for('post.index', fn (Generator $trail) => $trail->push('Posts', route('post.index')));

        // Posts > Upload Post
        Breadcrumbs::for('post.create', fn (Generator $trail) => $trail->parent('post.index')->push('New Post', route('post.create')));

        // Posts > [Post Name]
        Breadcrumbs::for('post.show', fn (Generator $trail, Post $post) => $trail->parent('post.index')->push($post->title, route('post.show', $post->id)));

        // Posts > [Post Name] > Edit Post
        Breadcrumbs::for('post.edit', fn (Generator $trail, Post $post) => $trail->parent('post.show', $post)->push('Edit Post', route('post.edit', $post->id)));

        $html = $this->get('/post/1/edit')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }
}
