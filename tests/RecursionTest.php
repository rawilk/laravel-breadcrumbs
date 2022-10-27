<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->domain = 'http://localhost';

    // Blog
    Breadcrumbs::for('blog', fn (Generator $trail) => $trail->push('Blog', url('/')));

    $this->category1 = (object) ['id' => 1, 'title' => 'Category 1'];
    $this->category2 = (object) ['id' => 2, 'title' => 'Category 2'];
    $this->category3 = (object) ['id' => 3, 'title' => 'Category 3'];
});

test('parents can be pushed repeatedly', function () {
    Breadcrumbs::for('category', function (Generator $trail, object $category) {
        $trail->parent('blog');

        foreach ($category->parents as $parent) {
            $trail->push($parent->title, url("category/{$parent->id}"));
        }

        $trail->push($category->title, url("category/{$category->id}"));
    });

    $this->category3->parents = [$this->category1, $this->category2];

    Route::get('/category', fn () => Breadcrumbs::render('category', $this->category3));

    get('/category')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 4)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => $this->domain,
                        'text' => 'Blog',
                    ]);
                })
                ->find('li:nth-of-type(2)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/category/1",
                        'text' => 'Category 1',
                    ]);
                })
                ->find('li:nth-of-type(3)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/category/2",
                        'text' => 'Category 2',
                    ]);
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Category 3')
                        ->doesntContain('a');
                });
        });
});

test('a trail parent can be called recursively', function () {
    Breadcrumbs::for('category', function (Generator $trail, object $category) {
        if ($category->parent) {
            $trail->parent('category', $category->parent);
        } else {
            $trail->parent('blog');
        }

        $trail->push($category->title, url("category/{$category->id}"));
    });

    $this->category1->parent = null;
    $this->category2->parent = $this->category1;
    $this->category3->parent = $this->category2;

    Route::get('/category', fn () => Breadcrumbs::render('category', $this->category3));

    get('/category')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 4)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => $this->domain,
                        'text' => 'Blog',
                    ]);
                })
                ->find('li:nth-of-type(2)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/category/1",
                        'text' => 'Category 1',
                    ]);
                })
                ->find('li:nth-of-type(3)', function (AssertElement $li) {
                    $li->contains('a', [
                        'href' => "{$this->domain}/category/2",
                        'text' => 'Category 2',
                    ]);
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Category 3')
                        ->doesntContain('a');
                });
        });
});
