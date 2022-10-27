<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->domain = 'http://localhost';

    // Home (normal link)
    Breadcrumbs::for(
        'home',
        fn (Generator $trail) => $trail->push('Home', url('/'))
    );

    // Home > Blog (not a link)
    Breadcrumbs::for(
        'blog',
        fn (Generator $trail) => $trail->parent('home')->push('Blog')
    );

    // Home > Blog > [Category] (Active page)
    Breadcrumbs::for(
        'category',
        fn (Generator $trail, $category) => $trail->parent('blog')->push($category->title, url('blog/category/' . $category->id))
    );

    $this->category = (object) [
        'id' => 123,
        'title' => 'Example Category',
    ];
});

it('renders breadcrumbs with params', function () {
    // {!! Breadcrumbs::render('category', $category) !!}
    Route::get('/category/{category}', fn () => view('category')->with('category', $this->category))->name('category');

    get('/category/123')
        ->assertElementExists('nav > ol', function (AssertElement $ol) {
            $ol->contains('li', 3)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->is('li')
                        ->contains('a', [
                            'href' => $this->domain,
                            'text' => 'Home',
                        ]);
                })
                ->find('li:nth-of-type(2)', function (AssertElement $li) {
                    $li->has('text', 'Blog')
                        ->doesntContain('a');
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Example Category');
                });
        });
});

it('renders in plain php views', function () {
    // <?php echo Breadcrumbs::render('category', $category);
    Route::get('/test', fn () => view('view-php')->with('category', $this->category))->name('test');

    get('/test')
        ->assertElementExists('nav#php-view-nav', function (AssertElement $nav) {
            $nav->contains('ol', 1)
                ->find('ol', function (AssertElement $ol) {
                    $ol->contains('li', 3)
                        ->find('li.current', function (AssertElement $li) {
                            $li->has('text', 'Example Category')
                                ->doesntContain('a');
                        });
                });
        });
});
