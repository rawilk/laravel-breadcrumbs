<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsViewNotSet;
use Rawilk\Breadcrumbs\Exceptions\UnnamedRoute;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Fixtures\app\Http\Controllers\PostsController;
use function Pest\Laravel\get;

it('shows a solution for duplicate breadcrumbs', function (array $files) {
    config([
        'breadcrumbs.files' => $files,
    ]);

    Breadcrumbs::for('duplicate', fn () => '');

    try {
        Breadcrumbs::for('duplicate', fn () => '');

        $this->fail('No exception thrown.');
    } catch (BreadcrumbAlreadyDefined $e) {
        $solution = $e->getSolution();

        expect($solution->getSolutionTitle())->toBe('Remove the duplicate breadcrumb')
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Defining Breadcrumbs',
                'Laravel Breadcrumbs Documentation',
            ]);

        if (count($files) === 1) {
            expect($solution->getSolutionDescription())->toEqual("Look in `{$files[0]}` for multiple breadcrumbs named `duplicate`.");
        } else {
            expect($solution->getSolutionDescription())->toContain(...array_values($files));
        }
    }
})->with(oneOrManyConfigFiles());

it('shows a solution for missing breadcrumbs', function (array $files) {
    config([
        'breadcrumbs.files' => $files,
        'breadcrumbs.exceptions.not_registered' => true,
    ]);

    try {
        Breadcrumbs::render('missing');

        $this->fail('No exception thrown.');
    } catch (BreadcrumbsNotRegistered $e) {
        $solution = $e->getSolution();

        expect($solution->getSolutionTitle())->toContain('Add this to')
            ->and($solution->getSolutionDescription())->toContain("Breadcrumbs::for('missing'")
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Defining Breadcrumbs',
                'Silencing Breadcrumb Exceptions',
            ]);
    }
})->with(oneOrManyConfigFiles());

it('shows a solution for view not set', function () {
    config([
        'breadcrumbs.view' => '',
    ]);

    Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', url('/')));

    try {
        Breadcrumbs::render('home');

        $this->fail('No exception thrown');
    } catch (BreadcrumbsViewNotSet $e) {
        $solution = $e->getSolution();

        expect($solution->getSolutionTitle())->toEqual('Set a view for Laravel Breadcrumbs')
            ->and($solution->getSolutionDescription())->toEqual("Please check `config/breadcrumbs.php` for a valid `'view'` (e.g. `breadcrumbs::tailwind`)")
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Choosing A Breadcrumbs Template (view)',
                'Laravel Breadcrumbs Documentation',
            ]);
    }
});

it('shows a solution for unnamed routes with closures', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => true,
    ]);

    Route::get('/blog', fn () => Breadcrumbs::render());

    try {
        get('/blog');

        $this->fail('No exception thrown');
    } catch (UnnamedRoute $e) {
        $solution = $e->getSolution();

        expect($solution->getSolutionTitle())->toEqual('Give the route a name.')
            ->and($solution->getSolutionDescription())->toContain("Route::get('/blog', function()")
            ->and($solution->getSolutionDescription())->toContain("->name('sample-name')")
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Route-Bound Breadcrumbs',
                'Silencing Breadcrumb Exceptions',
                'Laravel Breadcrumbs Documentation',
            ]);
    }
});

it('shows a solution for unnamed routes with a controller', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => true,
    ]);

    Route::get('/posts/{post}', [PostsController::class, 'edit']);

    try {
        get('/posts/1');

        $this->fail('No exception thrown');
    } catch (UnnamedRoute $e) {
        $solution = $e->getSolution();

        expect($solution->getSolutionTitle())->toEqual('Give the route a name.')
            ->and($solution->getSolutionDescription())->toContain("Route::get('/posts/{post}', '" . PostsController::class . "@edit')")
            ->and($solution->getSolutionDescription())->toContain("->name('sample-name')")
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Route-Bound Breadcrumbs',
                'Silencing Breadcrumb Exceptions',
                'Laravel Breadcrumbs Documentation',
            ]);
    }
});

it('shows a solution for unnamed routes using route view', function () {
    config([
        'breadcrumbs.exceptions.unnamed_route' => true,
    ]);

    Route::view('/blog', 'page');

    try {
        get('/blog');

        $this->fail('No exception thrown');
    } catch (ErrorException $e) {
        $solution = $e->getPrevious()->getSolution();

        expect($solution->getSolutionTitle())->toEqual('Give the route a name.')
            ->and($solution->getSolutionDescription())->toContain("Route::view('/blog', 'page')")
            ->and($solution->getSolutionDescription())->toContain("->name('sample-name')")
            ->and($solution->getDocumentationLinks())->toHaveKeys([
                'Route-Bound Breadcrumbs',
                'Silencing Breadcrumb Exceptions',
                'Laravel Breadcrumbs Documentation',
            ]);
    }
});

// Data Providers
function oneOrManyConfigFiles(): array
{
    return [
        'Single Config File' => [['routes/breadcrumbs.php']],
        'Multiple Config Files' => [['breadcrumbs/file1.php', 'breadcrumbs/file2.php']],
    ];
}
