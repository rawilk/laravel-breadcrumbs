<?php

declare(strict_types=1);

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Fixtures\app\Models\Post;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

beforeEach(function () {
    $this->domain = 'http://localhost';

    createPostsTable();
    createPost();
});

afterEach(function () {
    dropPostsTable();
});

it('renders breadcrumbs with no url', function () {
    Breadcrumbs::for('sample', fn (Generator $trail) => $trail->push('Sample'));

    $breadcrumbs = Breadcrumbs::generate('sample');

    expect($breadcrumbs)->count()->toBe(1)
        ->and($breadcrumbs[0]->title)->toBe('Sample')
        ->and($breadcrumbs[0]->url)->toBeNull();
});

it('accepts custom data', function () {
    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', '/', ['icon' => 'home.png']));

    $breadcrumbs = Breadcrumbs::generate('home');

    expect($breadcrumbs)->count()->toBe(1)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe('/')
        ->and($breadcrumbs[0]->icon)->toBe('home.png');
});

test('breadcrumbs can be automatically prepended', function () {
    Breadcrumbs::before(fn (Generator $trail) => $trail->push('Before'));

    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

    Route::get('/', fn () => Breadcrumbs::render('home'))->name('home');

    get('/')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->has('text', 'Before');
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Home')
                        ->doesntContain('a');
                });
        });
});

it('can get the current breadcrumb\'s title', function () {
    Route::get('/', fn () => '')->name('home');
    Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::current()->title)->middleware(SubstituteBindings::class)->name('post');

    Breadcrumbs::for('post', function (Generator $trail, Post $post) {
        $trail
            ->push('Home', route('home'))
            ->push($post->title, route('post', $post))
            ->push('Page 2', null, ['current' => false]);
    });

    get('/post/1')->assertSee('Post 1');
});

it('generates a collection of breadcrumbs', function () {
    Route::get('/', fn () => '')->name('home');
    Route::get('/post/{post}', fn () => '')->name('post');

    Breadcrumbs::for('post', function (Generator $trail, $id) {
        $trail
            ->push('Home', route('home'))
            ->push("Post {$id}", route('post', $id))
            ->push('Page 2', null, ['current' => false]);
    });

    $breadcrumbs = Breadcrumbs::generate('post', 1)->where('current', '!==', false);

    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs->last()->title)->toBe('Post 1');
});

it('is macroable', function () {
    Route::get('/', fn () => '')->name('home');
    Route::get('/post/{post}', fn (Post $post) => Breadcrumbs::pageTitle())->middleware(SubstituteBindings::class)->name('post');

    Breadcrumbs::for('post', static function (Generator $trail, Post $post) {
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

    get('/post/1?page=2')
        ->assertSee('Post 1 - Page 2 - Acme');
});

test('other breadcrumbs can be created in macros', function () {
    Route::get('/', fn () => '')->name('home');
    Route::get('/blog', fn () => '')->name('blog.index');
    Route::get('/blog/create', fn () => '')->name('blog.create');
    Route::post('/blog', fn () => '')->name('blog.store');
    Route::get('/blog/{post}', fn (Post $post) => '')->middleware(SubstituteBindings::class)->name('blog.show');
    Route::get('/blog/{post}/edit', fn (Post $post) => '')->middleware(SubstituteBindings::class)->name('blog.edit');
    Route::put('/blog/{post}', fn (Post $post) => '')->middleware(SubstituteBindings::class)->name('blog.update');
    Route::delete('/blog/{post}', fn (Post $post) => '')->middleware(SubstituteBindings::class)->name('blog.destroy');

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
    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs)->count()->toBe(2)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Blog')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/blog");

    // Create
    $breadcrumbs = Breadcrumbs::generate('blog.create');
    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs)->count()->toBe(3)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Blog')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/blog")
        ->and($breadcrumbs[2]->title)->toBe('New')
        ->and($breadcrumbs[2]->url)->toBe("{$this->domain}/blog/create");

    // Show
    $breadcrumbs = Breadcrumbs::generate('blog.show', Post::find(1));
    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs)->count()->toBe(3)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Blog')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/blog")
        ->and($breadcrumbs[2]->title)->toBe('Post 1')
        ->and($breadcrumbs[2]->url)->toBe("{$this->domain}/blog/1");

    // Edit
    $breadcrumbs = Breadcrumbs::generate('blog.edit', Post::find(1));
    expect($breadcrumbs)->toBeInstanceOf(Collection::class)
        ->and($breadcrumbs)->count()->toBe(4)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Blog')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/blog")
        ->and($breadcrumbs[2]->title)->toBe('Post 1')
        ->and($breadcrumbs[2]->url)->toBe("{$this->domain}/blog/1")
        ->and($breadcrumbs[3]->title)->toBe('Edit')
        ->and($breadcrumbs[3]->url)->toBe("{$this->domain}/blog/1/edit");
});

test('the current route can be set manually', function () {
    Breadcrumbs::for('sample', fn (Generator $trail) => $trail->push('Sample'));
    Breadcrumbs::setCurrentRoute('sample');

    Route::get('/sample', fn () => Breadcrumbs::render())->name('sample');

    get('/sample')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Sample')
                        ->doesntContain('a');
                });
        });
});

test('the current route can be set with params', function () {
    Breadcrumbs::for('sample', fn (Generator $trail, $a, $b) => $trail->push("Sample {$a}, {$b}"));
    Breadcrumbs::setCurrentRoute('sample', 1, 2);

    Route::get('/sample', fn () => Breadcrumbs::render())->name('sample');

    get('/sample')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Sample 1, 2');
                });
        });
});

test('the current route can be cleared', function () {
    Breadcrumbs::for('sample', fn (Generator $trail, $a, $b) => $trail->push("Sample {$a}, {$b}"));
    Breadcrumbs::setCurrentRoute('sample', 1, 2);
    Breadcrumbs::clearCurrentRoute();
    Breadcrumbs::render();
})->expectException(BreadcrumbsNotRegistered::class);
