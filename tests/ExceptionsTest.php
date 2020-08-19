<?php

namespace Rawilk\Breadcrumbs\Tests;

use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsViewNotSet;
use Rawilk\Breadcrumbs\Exceptions\UnnamedRoute;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Spatie\Snapshots\MatchesSnapshots;

class ExceptionsTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_throws_an_exception_when_a_breadcrumb_is_defined_twice(): void
    {
        $this->expectException(BreadcrumbAlreadyDefined::class);

        Breadcrumbs::for('duplicate', function () {});
        Breadcrumbs::for('duplicate', function () {});
    }

    /** @test */
    public function it_throws_an_exception_if_a_breadcrumb_is_not_found(): void
    {
        config([
            'breadcrumbs.exceptions.not_registered' => true,
        ]);

        $this->expectException(BreadcrumbsNotRegistered::class);

        Breadcrumbs::render('not-defined');
    }

    /** @test */
    public function the_breadcrumb_not_found_exception_can_be_disabled_via_config(): void
    {
        config([
            'breadcrumbs.exceptions.not_registered' => false,
        ]);

        $html = Breadcrumbs::render('not-defined');

        // <p>No breadcrumbs</p>
        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_throws_an_exception_if_the_breadcrumbs_view_is_not_set(): void
    {
        config([
            'breadcrumbs.view' => '',
        ]);

        $this->expectException(BreadcrumbsViewNotSet::class);

        Breadcrumbs::for('home', fn ($trail) => $trail->push('Home', url('/')));

        Breadcrumbs::render('home');
    }

    /** @test */
    public function it_throws_exceptions_for_missing_route_bound_breadcrumbs(): void
    {
        config([
            'breadcrumbs.exceptions.missing_route_bound_breadcrumb' => true,
        ]);

        $this->expectException(BreadcrumbsNotRegistered::class);

        Route::get('/', fn () => Breadcrumbs::render())->name('home');

        $this->get('/');
    }

    /** @test */
    public function it_will_not_throw_an_exception_for_missing_route_bound_breadcrumbs_if_disabled_in_the_config(): void
    {
        config([
            'breadcrumbs.exceptions.missing_route_bound_breadcrumb' => false,
        ]);

        Route::get('/', fn () => Breadcrumbs::render())->name('home');

        $html = $this->get('/')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_throws_an_exception_for_route_bound_routes_if_the_current_route_is_not_named(): void
    {
        config([
            'breadcrumbs.exceptions.unnamed_route' => true,
        ]);

        $this->expectException(UnnamedRoute::class);

        Route::get('/blog', fn () => Breadcrumbs::render());

        $this->get('/blog');
    }

    /** @test */
    public function it_throws_an_exception_on_the_home_route_if_it_is_not_named(): void
    {
        config([
            'breadcrumbs.exceptions.unnamed_route' => true,
        ]);

        $this->expectException(UnnamedRoute::class);

        Route::get('/', fn () => Breadcrumbs::render());

        $this->get('/');
    }

    /** @test */
    public function the_unnamed_route_exception_can_be_disabled_via_config(): void
    {
        config([
            'breadcrumbs.exceptions.unnamed_route' => false,
        ]);

        Route::get('/', fn () => Breadcrumbs::render());

        $html = $this->get('/')->content();

        $this->assertMatchesHtmlSnapshot($html);
    }
}
