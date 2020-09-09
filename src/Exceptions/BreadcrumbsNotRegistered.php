<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Rawilk\Breadcrumbs\Concerns\GetsConfigBreadcrumbFiles;
use Rawilk\Breadcrumbs\Support\IgnitionLinks;

class BreadcrumbsNotRegistered extends Exception implements ProvidesSolution
{
    use GetsConfigBreadcrumbFiles;

    protected bool $isRouteBound = false;
    protected string $name;

    public function __construct(string $name)
    {
        parent::__construct("No breadcrumbs defined for '{$name}'.");

        $this->name = $name;
    }

    public function isRouteBound(): self
    {
        $this->isRouteBound = true;

        return $this;
    }

    public function getSolution(): Solution
    {
        $files = $this->getBreadcrumbFiles();

        if (count($files) === 1) {
            $file = Str::replaceFirst(base_path() . DIRECTORY_SEPARATOR, '', $files[0]);
        } else {
            $file = 'one of the files defined in config/breadcrumbs.php';
        }

        // Determine the current route name
        $route = Route::current();
        $routeName = $route ? $route->getName() : null;
        if ($routeName) {
            $url = "route('{$this->name}')";
        } else {
            $url = "url('" . Request::path() . "')";
        }

        $links = [
            'Defining Breadcrumbs' => IgnitionLinks::DEFINING_BREADCRUMBS,
        ];

        if ($this->isRouteBound) {
            $links['Route-Bound Breadcrumbs'] = IgnitionLinks::ROUTE_BOUND_BREADCRUMBS;
        }

        $links['Silencing Breadcrumb Exceptions'] = IgnitionLinks::CONFIG;
        $links['Laravel Breadcrumbs Documentation'] = IgnitionLinks::DOCS;

        return BaseSolution::create("Add this to {$file}")
            ->setSolutionDescription("
```php
Breadcrumbs::for('{$this->name}', fn (Generator \$trail) => \$trail->push('Title Here', {$url}));
```")
            ->setDocumentationLinks($links);
    }
}
