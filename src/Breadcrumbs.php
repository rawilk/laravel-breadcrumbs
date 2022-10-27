<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Rawilk\Breadcrumbs\Contracts\Generator;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsViewNotSet;
use Rawilk\Breadcrumbs\Exceptions\UnnamedRoute;

class Breadcrumbs
{
    use Macroable;

    protected array $callbacks = [];
    protected array $before = [];
    protected ?array $route = null;

    public function __construct(
        protected Generator $generator,
        protected Router $router,
        protected ViewFactory $viewFactory
    ) {
    }

    /**
     * Register a breadcrumb-generating callback for a page.
     *
     * @param string $name
     * @param callable $callback
     * @throws \Rawilk\Breadcrumbs\Exceptions\BreadcrumbAlreadyDefined
     */
    public function for(string $name, callable $callback): void
    {
        if (isset($this->callbacks[$name])) {
            throw new BreadcrumbAlreadyDefined($name);
        }

        $this->callbacks[$name] = $callback;
    }

    /**
     * Register a closure to call to generate a breadcrumbs item before each page.
     * Useful for prepending pages like a "home" page automatically each time.
     *
     * @param callable $callback
     */
    public function before(callable $callback): void
    {
        $this->before[] = $callback;
    }

    public function exists(string $name = null): bool
    {
        if (is_null($name)) {
            try {
                [$name] = $this->getCurrentRoute();
            } catch (UnnamedRoute) {
                return false;
            }
        }

        return isset($this->callbacks[$name]);
    }

    public function generate(?string $name = null, ...$params): Collection
    {
        $originalName = $name;

        // Use the current route name if no name specified.
        if ($name === null) {
            try {
                [$name, $params] = $this->getCurrentRoute();
            } catch (UnnamedRoute $e) {
                if (config('breadcrumbs.exceptions.unnamed_route')) {
                    throw $e;
                }

                return new Collection;
            }
        }

        try {
            return $this->generator->generate($this->callbacks, $this->before, $name, $params);
        } catch (BreadcrumbsNotRegistered $e) {
            if ($originalName === null && config('breadcrumbs.exceptions.missing_route_bound_breadcrumb')) {
                $e->isRouteBound();

                throw $e;
            }

            if ($originalName !== null && config('breadcrumbs.exceptions.not_registered')) {
                throw $e;
            }

            return new Collection;
        }
    }

    public function view(string $view, ?string $name = null, ...$params): string
    {
        $breadcrumbs = $this->generate($name, ...$params);

        return $this->viewFactory->make($view, compact('breadcrumbs'))->render();
    }

    public function render(?string $name = null, ...$params): string
    {
        $view = config('breadcrumbs.view');

        if (! $view) {
            throw new BreadcrumbsViewNotSet('Breadcrumbs view not defined in: config/breadcrumbs.php');
        }

        return $this->view($view, $name, ...$params);
    }

    public function current(): ?object
    {
        return $this->generate()->where('current', '!==', false)->last();
    }

    /**
     * Get the current route name and parameters.
     *
     * @return array
     * @throws \Rawilk\Breadcrumbs\Exceptions\UnnamedRoute
     */
    protected function getCurrentRoute(): array
    {
        // The route has been set manually
        if ($this->route) {
            return $this->route;
        }

        $route = $this->router->current();

        // Possibly a 404...
        if ($route === null) {
            return ['errors.404', []];
        }

        $name = $route->getName();

        if ($name === null) {
            throw new UnnamedRoute($route);
        }

        $params = array_values($route->parameters());

        return [$name, $params];
    }

    public function setCurrentRoute(string $name, ...$params): self
    {
        $this->route = [$name, $params];

        return $this;
    }

    public function clearCurrentRoute(): self
    {
        $this->route = null;

        return $this;
    }
}
