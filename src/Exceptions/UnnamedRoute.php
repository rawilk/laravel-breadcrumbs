<?php

namespace Rawilk\Breadcrumbs\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Rawilk\Breadcrumbs\Support\IgnitionLinks;

class UnnamedRoute extends Exception implements ProvidesSolution
{
    protected Route $route;

    public function __construct(Route $route)
    {
        $uri = Arr::first($route->methods()) . ' /' . ltrim($route->uri(), '/');

        parent::__construct("The current route ({$uri}) is not named.");

        $this->route = $route;
    }

    public function getSolution(): Solution
    {
        $method = strtolower(Arr::first($this->route->methods()));
        $uri = $this->route->uri();
        $action = $this->route->getActionName();

        if ($action === '\Illuminate\Routing\ViewController') {
            $method = 'view';
            $action = "'" . ($this->route->defaults['view'] ?? 'view-name') . "'";
        } elseif ($action === 'Closure') {
            $action = "function() {\n    ...\n}";
        } else {
            $action = "'" . Str::replaceFirst(App::getNamespace() . 'Http\Controllers\\', '', $action) . "'";
        }

        $links = [
            'Route-Bound Breadcrumbs' => IgnitionLinks::ROUTE_BOUND_BREADCRUMBS,
            'Silencing Breadcrumb Exceptions' => IgnitionLinks::CONFIG,
            'Laravel Breadcrumbs Documentation' => IgnitionLinks::DOCS,
        ];

        return BaseSolution::create('Give the route a name.')
            ->setSolutionDescription("For example:

```php
Route::{$method}('{$uri}', {$action})->name('sample-name');
```")
            ->setDocumentationLinks($links);
    }
}
