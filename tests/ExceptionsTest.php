<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsViewNotSet;
use Rawilk\Breadcrumbs\Exceptions\UnnamedRoute;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('throws an exception when a breadcrumb is defined twice', function () {
    Breadcrumbs::for('duplicate', function () {
    });

    Breadcrumbs::for('duplicate', function () {
    });
})->throws(BreadcrumbAlreadyDefined::class);

it('throws an exception if a breadcrumb is not found', function () {
    config([
        'breadcrumbs.exceptions.not_registered' => true,
    ]);

    Breadcrumbs::render('not-defined');
})->throws(BreadcrumbsNotRegistered::class);

test('the breadcrumb not found exception can be disabled via config', function () {
    config([
        'breadcrumbs.exceptions.not_registered' => false,
    ]);

    // Will render <p>No breadcrumbs</p> from our test fixtures directory.
    Route::get('/test', fn () => Breadcrumbs::render('not-defined'));

    get('/test')
        ->assertElementExists('p', function (AssertElement $p) {
            $p->has('text', 'No breadcrumbs');
        });
});

it('throws an exception if the breadcrumbs view is not set', function () {
    config([
        'breadcrumbs.view' => '',
    ]);

    Breadcrumbs::for('home', fn ($trail) => $trail->push('Home', url('/')));

    Breadcrumbs::render('home');
})->expectException(BreadcrumbsViewNotSet::class);

it('throws an exception for missing route bound breadcrumbs', function () {
    config([
        'breadcrumbs.exceptions.missing_route_bound_breadcrumb' => true,
    ]);

    Route::get('/', fn () => Breadcrumbs::render())->name('home');

    get('/');
})->expectException(BreadcrumbsNotRegistered::class);

it('will not throw an exception for missing route bound breadcrumbs if disabled in the config', function () {
    config([
        'breadcrumbs.exceptions.missing_route_bound_breadcrumb' => false,
    ]);

    Route::get('/', fn () => Breadcrumbs::render())->name('home');

    get('/')
        ->assertElementExists('p', function (AssertElement $p) {
            $p->has('text', 'No breadcrumbs');
        });
});

it('throws an exception for route bound routes if the current route is not named', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => true,
    ]);

    Route::get('/blog', fn () => Breadcrumbs::render());

    get('/blog');
})->expectException(UnnamedRoute::class);

it('throws an exception on the home route if it is not named', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => true,
    ]);

    Route::get('/', fn () => Breadcrumbs::render());

    get('/');
})->expectException(UnnamedRoute::class);

test('the unnamed route exception can be disabled via config', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => false,
    ]);

    Route::get('/', fn () => Breadcrumbs::render());

    get('/')
        ->assertElementExists('p', function (AssertElement $p) {
            $p->has('text', 'No breadcrumbs');
        });
});
