<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->domain = 'http://localhost';

    $closure = fn () => '';

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
});

it('generates breadcrumbs', function () {
    $breadcrumbs = Breadcrumbs::generate('post', $this->post);

    expect($breadcrumbs)->count()->toBe(4)
        ->and($breadcrumbs[0]->title)->toBe('Home')
        ->and($breadcrumbs[0]->url)->toBe($this->domain)
        ->and($breadcrumbs[1]->title)->toBe('Blog')
        ->and($breadcrumbs[1]->url)->toBe("{$this->domain}/blog")
        ->and($breadcrumbs[2]->title)->toBe('Example Category')
        ->and($breadcrumbs[2]->url)->toBe("{$this->domain}/blog/category/123")
        ->and($breadcrumbs[3]->title)->toBe('Sample Post')
        ->and($breadcrumbs[3]->url)->toBe("{$this->domain}/blog/post/456");
});

test('a single breadcrumb can be rendered', function () {
    Route::get('/test', fn () => Breadcrumbs::render('home'));

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li.current', [
                'text' => 'Home',
            ]);
        });
});

test('a breadcrumb can be rendered with a parent', function () {
    Route::get('/test', fn () => Breadcrumbs::render('blog'));

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', [
                'text' => 'Home',
            ])
            ->contains('li.current', [
                'text' => 'Blog',
            ])
            ->doesntContain('li.current', [
                'text' => 'Home',
            ])
            ->contains('li', 2);
        });
});

it('accepts parameters when rendering', function () {
    Route::get('/test', fn () => Breadcrumbs::render('category', $this->category));

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 3)
                ->contains('li.current', 1)
                ->contains('li.current', [
                    'text' => 'Example Category',
                ]);
        });
});

it('can render multiple levels of dynamic breadcrumbs', function () {
    Route::get('/test', fn () => Breadcrumbs::render('post', $this->post));

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 4)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(3)', function (AssertElement $li) {
                    $li->is('li')
                        ->contains('a', [
                            'text' => 'Example Category',
                            'href' => "{$this->domain}/blog/category/123",
                        ]);
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->is('li')
                        ->has('text', 'Sample Post')
                        ->doesntContain('a');
                });
        });
});
