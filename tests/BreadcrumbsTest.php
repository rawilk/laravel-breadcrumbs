<?php

namespace Rawilk\Breadcrumbs\Tests;

use Illuminate\Support\Facades\Route;
use LogicException;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Spatie\Snapshots\MatchesSnapshots;

class BreadcrumbsTest extends TestCase
{
    use MatchesSnapshots;

    protected object $category;
    protected object $post;

    protected function setUp(): void
    {
        parent::setUp();

        $closure = function () {
            throw new LogicException;
        };

        Route::get('/', $closure)->name('home');

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

        // Home > About
        Route::get('about', $closure)->name('about');

        Breadcrumbs::for('about', fn (Generator $trail) => $trail->parent('home')->push('About', route('about')));

        // Home > Blog
        Route::get('blog', $closure)->name('blog');

        Breadcrumbs::for('blog', fn (Generator $trail) => $trail->parent('home')->push('Blog', route('blog')));

        // Home > Blog > [Category]
        Route::get('blog/category/{category}', $closure)->name('category');

        Breadcrumbs::for('category', fn (Generator $trail, $category) => $trail->parent('blog')->push($category->title, route('category', $category->id)));

        // Home > Blog > [Category] > [Post]
        Route::get('blog/post/{post}', $closure)->name('post');

        Breadcrumbs::for('post', fn (Generator $trail, $post) => $trail->parent('category', $post->category)->push($post->title, route('post', $post->id)));

        $this->category = (object) [
            'id' => 123,
            'title' => 'Example Category',
        ];

        $this->post = (object) [
            'id' => 456,
            'title' => 'Sample Post',
            'category' => $this->category,
        ];
    }

    /** @test */
    public function it_generates_breadcrumbs(): void
    {
        $breadcrumbs = Breadcrumbs::generate('post', $this->post);

        self::assertCount(4, $breadcrumbs);

        self::assertSame('Home', $breadcrumbs[0]->title);
        self::assertSame('http://localhost', $breadcrumbs[0]->url);

        self::assertSame('Blog', $breadcrumbs[1]->title);
        self::assertSame('http://localhost/blog', $breadcrumbs[1]->url);

        self::assertSame('Example Category', $breadcrumbs[2]->title);
        self::assertSame('http://localhost/blog/category/123', $breadcrumbs[2]->url);

        self::assertSame('Sample Post', $breadcrumbs[3]->title);
        self::assertSame('http://localhost/blog/post/456', $breadcrumbs[3]->url);
    }

    /** @test */
    public function a_single_breadcrumb_can_be_rendered(): void
    {
        $html = Breadcrumbs::render('home');

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function a_breadcrumb_can_be_rendered_with_a_parent(): void
    {
        $html = Breadcrumbs::render('blog');

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_accepts_parameters_when_rendering(): void
    {
        $html = Breadcrumbs::render('category', $this->category);

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_can_render_multiple_levels_of_dynamic_breadcrumbs(): void
    {
        $html = Breadcrumbs::render('post', $this->post);

        $this->assertMatchesHtmlSnapshot($html);
    }
}
