<?php

namespace Rawilk\Breadcrumbs\Tests;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Concerns\NeedsDatabase;
use Rawilk\Breadcrumbs\Tests\Models\Post;
use Spatie\Snapshots\MatchesSnapshots;

class AdvancedUsageTest extends TestCase
{
    use MatchesSnapshots, NeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /** @test */
    public function it_renders_breadcrumbs_with_no_url(): void
    {
        Breadcrumbs::for('sample', fn (Generator $trail) => $trail->push('Sample'));

        $breadcrumbs = Breadcrumbs::generate('sample');

        self::assertCount(1, $breadcrumbs);
        self::assertSame('Sample', $breadcrumbs[0]->title);
        self::assertNull($breadcrumbs[0]->url);
    }

    /** @test */
    public function it_accepts_custom_data(): void
    {
        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', '/', ['icon' => 'home.png']));

        $breadcrumbs = Breadcrumbs::generate('home');

        self::assertCount(1, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('/', $breadcrumbs[0]->url);
        self::assertSame('home.png', $breadcrumbs[0]->icon);
    }

    /** @test */
    public function breadcrumbs_can_be_automatically_prepended(): void
    {
        Breadcrumbs::before(fn (Generator $trail) => $trail->push('Before'));

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        Route::get('/', fn () => Breadcrumbs::render('home'))->name('home');

        $html = $this->get('/')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_get_the_current_breadcrumbs_title(): void
    {
        Route::get('/', function () {
        })->name('home');

        Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::current()->title)->middleware(SubstituteBindings::class)->name('post');

        Breadcrumbs::for('post', static function (Generator $trail, $post) {
            $trail
                ->push('Home', route('home'))
                ->push($post->title, route('post', $post))
                ->push('Page 2', null, ['current' => false]);
        });

        $html = $this->get('/post/1')->content();

        self::assertSame('Post 1', $html);
    }

    /** @test */
    public function it_generates_a_collection_of_breadcrumbs(): void
    {
        Route::get('/', function () {
        })->name('home');
        Route::get('/post/{post}', function () {
        })->name('post');

        Breadcrumbs::for('post', static function (Generator $trail, $id) {
            $trail
                ->push('Home', route('home'))
                ->push("Post {$id}", route('post', $id))
                ->push('Page 2', null, ['current' => false]);
        });

        $breadcrumbs = Breadcrumbs::generate('post', 1)->where('current', '!==', false);

        self::assertInstanceOf(Collection::class, $breadcrumbs);
        self::assertSame('Post 1', $breadcrumbs->last()->title);
    }

    /** @test */
    public function it_is_macroable(): void
    {
        Route::get('/', function () {
        })->name('home');

        Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::pageTitle())->middleware(SubstituteBindings::class)->name('post');

        Breadcrumbs::for('post', static function (Generator $trail, $post) {
            $trail
                ->push('Home', route('home'))
                ->push($post->title, route('post', $post))
                ->push('Page 2', null, ['current' => false]);
        });

        Breadcrumbs::macro('pageTitle', function () {
            $title = ($breadcrumb = $this->current()) ? "{$breadcrumb->title} - " : '';

            if (($page = (int) request('page')) > 1) {
                $title .= "Page {$page} - ";
            }

            return $title . 'Acme';
        });

        $html = $this->get('/post/1?page=2')->content();

        self::assertSame('Post 1 - Page 2 - Acme', $html);
    }

    /** @test */
    public function other_breadcrumbs_can_be_created_in_macros(): void
    {
        Route::get('/', function () {
        })->name('home');
        Route::get('/blog', function () {
        })->name('blog.index');
        Route::get('/blog/create', function () {
        })->name('blog.create');
        Route::post('/blog', function () {
        })->name('blog.store');
        Route::get('/blog/{post}', function (Post $post) {
        })->middleware(SubstituteBindings::class)->name('blog.show');
        Route::get('/blog/{post}/edit', function (Post $post) {
        })->middleware(SubstituteBindings::class)->name('blog.edit');
        Route::put('/blog/{post}', function (Post $post) {
        })->middleware(SubstituteBindings::class)->name('blog.update');
        Route::delete('/blog/{post}', function (Post $post) {
        })->middleware(SubstituteBindings::class)->name('blog.destroy');

        Breadcrumbs::macro('resource', function ($name, $title) {
            // Home > Blog
            $this->for("{$name}.index", fn (Generator $trail) => $trail->parent('home')->push($title, route("{$name}.index")));

            // Home > Blog > New
            $this->for("{$name}.create", fn (Generator $trail) => $trail->parent("{$name}.index")->push('New', route("{$name}.create")));

            // Home > Blog > Post 456
            $this->for("{$name}.show", fn (Generator $trail, $model) => $trail->parent("{$name}.index")->push($model->title, route("{$name}.show", $model)));

            // Home > Blog > Post 456 > Edit
            $this->for("{$name}.edit", fn (Generator $trail, $model) => $trail->parent("{$name}.show", $model)->push('Edit', route("{$name}.edit", $model)));
        });

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home'), ['icon' => 'home.png']));

        Breadcrumbs::resource('blog', 'Blog');

        // Index
        $breadcrumbs = Breadcrumbs::generate('blog.index');
        self::assertInstanceOf(Collection::class, $breadcrumbs);
        self::assertCount(2, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);
        self::assertSame('Blog', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/blog', $breadcrumbs[1]->url);

        // Create
        $breadcrumbs = Breadcrumbs::generate('blog.create');
        self::assertInstanceOf(Collection::class, $breadcrumbs);
        self::assertCount(3, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);
        self::assertSame('Blog', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        self::assertSame('New', $breadcrumbs[2]->title);
        self::assertSame('http://localhost/blog/create', $breadcrumbs[2]->url);

        // Show
        $breadcrumbs = Breadcrumbs::generate('blog.show', Post::find(1));
        self::assertInstanceOf(Collection::class, $breadcrumbs);
        self::assertCount(3, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);
        self::assertSame('Blog', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        self::assertSame('Post 1', $breadcrumbs[2]->title);
        self::assertSame('http://localhost/blog/1', $breadcrumbs[2]->url);

        // Edit
        $breadcrumbs = Breadcrumbs::generate('blog.edit', Post::find(1));
        self::assertInstanceOf(Collection::class, $breadcrumbs);
        self::assertCount(4, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);
        self::assertSame('Blog', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        self::assertSame('Post 1', $breadcrumbs[2]->title);
        self::assertSame('http://localhost/blog/1', $breadcrumbs[2]->url);
        self::assertSame('Edit', $breadcrumbs[3]->title);
        self::assertSame('http://localhost/blog/1/edit', $breadcrumbs[3]->url);
    }

    /** @test */
    public function the_current_route_can_be_set_manually(): void
    {
        Breadcrumbs::for('sample', fn (Generator $trail) => $trail->push('Sample'));

        Breadcrumbs::setCurrentRoute('sample');

        $html = Breadcrumbs::render();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function the_current_route_can_be_set_with_params(): void
    {
        Breadcrumbs::for('sample', fn (Generator $trail, $a, $b) => $trail->push("Sample {$a}, {$b}"));

        Breadcrumbs::setCurrentRoute('sample', 1, 2);

        $html = Breadcrumbs::render();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function the_current_route_can_be_cleared(): void
    {
        $this->expectException(BreadcrumbsNotRegistered::class);

        Breadcrumbs::for('sample', fn (Generator $trail, $a, $b) => $trail->push("Sample {$a}, {$b}"));

        Breadcrumbs::setCurrentRoute('sample', 1, 2);
        Breadcrumbs::clearCurrentRoute();

        Breadcrumbs::render();
    }
}
