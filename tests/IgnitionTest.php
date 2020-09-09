<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests;

use Facade\IgnitionContracts\ProvidesSolution;
use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsViewNotSet;
use Rawilk\Breadcrumbs\Exceptions\UnnamedRoute;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Http\Controllers\PostsController;
use Spatie\Snapshots\MatchesSnapshots;

class IgnitionTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @test
     * @dataProvider oneOrManyConfigFiles
     * @param array $files
     */
    public function it_shows_a_solution_for_duplicate_breadcrumbs(array $files): void
    {
        config([
            'breadcrumbs.files' => $files,
        ]);

        Breadcrumbs::for('duplicate', function () {
        });

        try {
            Breadcrumbs::for('duplicate', function () {
            });

            self::fail('No exception thrown.');
        } catch (BreadcrumbAlreadyDefined $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /**
     * @test
     * @dataProvider oneOrManyConfigFiles
     * @param array $files
     */
    public function it_shows_a_solution_for_missing_breadcrumbs($files): void
    {
        config([
            'breadcrumbs.files' => $files,
            'breadcrumbs.exceptions.not_registered' => true,
        ]);

        try {
            Breadcrumbs::render('missing');

            self::fail('No exception thrown');
        } catch (BreadcrumbsNotRegistered $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /**
     * @test
     * @dataProvider oneOrManyConfigFiles
     * @param array $files
     */
    public function it_shows_a_solution_for_missing_route_bound_breadcrumbs(array $files): void
    {
        config([
            'breadcrumbs.files' => $files,
            'breadcrumbs.exceptions.missing_route_bound_breadcrumb' => true,
        ]);

        Route::get('/', fn () => Breadcrumbs::render())->name('home');

        try {
            $this->get('/');

            self::fail('No exception thrown');
        } catch (BreadcrumbsNotRegistered $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /** @test */
    public function it_shows_a_solution_for_view_not_set(): void
    {
        config([
            'breadcrumbs.view' => '',
        ]);

        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', url('/')));

        try {
            Breadcrumbs::render('home');

            self::fail('No exception thrown');
        } catch (BreadcrumbsViewNotSet $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /** @test */
    public function it_shows_a_solution_for_unnamed_routes_with_closures(): void
    {
        config([
            'breadcrumbs.exceptions.unnamed_route' => true,
        ]);

        Route::get('/blog', fn () => Breadcrumbs::render());

        try {
            $this->get('/blog');

            self::fail('No exception thrown');
        } catch (UnnamedRoute $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /** @test */
    public function it_shows_a_solution_for_unnamed_routes_with_a_controller(): void
    {
        Route::get('/posts/{post}', [PostsController::class, 'edit']);

        try {
            $this->get('/posts/1');

            self::fail('No exception thrown.');
        } catch (UnnamedRoute $e) {
            $this->assertSolutionMatchesSnapshot($e);
        }
    }

    /** @test */
    public function it_shows_a_solution_for_unnamed_routes_using_route_view(): void
    {
        Route::view('/blog', 'page');

        try {
            $this->get('/blog');

            self::fail('No exception thrown');
        } catch (\ErrorException $e) {
            $this->assertSolutionMatchesSnapshot($e->getPrevious());
        }
    }

    protected function assertSolutionMatchesSnapshot(ProvidesSolution $exception): void
    {
        $solution = $exception->getSolution();

        $this->assertSnapshot($solution->getSolutionTitle());
        $this->assertSnapshot($solution->getSolutionDescription());
        $this->assertSnapshot($this->convertLinksToString($solution->getDocumentationLinks()));
    }

    protected function convertLinksToString(array $links): string
    {
        $string = '';

        foreach ($links as $key => $value) {
            $string .= "'{$key}': '{$value}'\n";
        }

        return $string;
    }

    protected function assertSnapshot(string $string): void
    {
        $string = str_replace("\r\n", "\n", $string);

        $this->assertMatchesSnapshot($string);
    }

    public function oneOrManyConfigFiles(): array
    {
        return [
            'Single Config File' => [['routes/breadcrumbs.php']],
            'Multiple Config Files' => [['breadcrumbs/file1.php', 'breadcrumbs/file2.php']],
        ];
    }
}
