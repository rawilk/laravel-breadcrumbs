---
title: Breadcrumbs Facade
sort: 1
---

### for
<x-code lang="php">
/**
 * Register a new breadcrumb.
 *
 * @param string $name
 * @param callable $callback
 * @return void
 */
Breadcrumbs::for(string $name, callable $callback): void;
</x-code>

### before
<x-code lang="php">
/**
 * Register a breadcrumb to be rendered before every breadcrumb.
 *
 * @param callable $callback
 * @return void
 */
Breadcrumbs::before(callable $callback): void;
</x-code>

### exists
<x-code lang="php">
/**
 * Check if a breadcrumb exists.
 * Omit name to check if a breadcrumb exists for the current route.
 *
 * @param string|null $name
 * @return bool
 */
Breadcrumbs::exists(string $name = null): bool;
</x-code>

### generate
<x-code lang="php">
/**
 * Generate breadcrumbs for the given registered breadcrumb, or the current route.
 *
 * @param string|null $name
 * @param ...$params
 * @return \Illuminate\Support\Collection
 */
Breadcrumbs::generate(string $name = null, ...$params): Collection;
</x-code>

### view
<x-code lang="php">
/**
 * Render a registered breadcrumb using the given view.
 *
 * @param string $view
 * @param string|null $name
 * @param ...$params
 * @return string
 */
Breadcrumbs::view(string $view, string $name = null, ...$params): string;
</x-code>

### render
<x-code lang="php">
/**
 * Render a registered breadcrumb.
 *
 * @param string|null $name
 * @param ...$params
 * @return string
 */
Breadcrumbs::render(string $name = null, ...$params): string;
</x-code>

### current
<x-code lang="php">
/**
 * Retrieve the breadcrumb for the current page.
 *
 * @return object|null
 */
Breadcrumbs::current(): ?object;
</x-code>

### setCurrentRoute
<x-code lang="php">
/**
 * Manually override the current route.
 *
 * @param string $name
 * @param ...$params
 * @return \Rawilk\Breadcrumbs\Breadcrumbs
 */
Breadcrumbs::setCurrentRoute(string $name, ...$params): \Rawilk\Breadcrumbs\Breadcrumbs;
</x-code>

### clearCurrentRoute
<x-code lang="php">
/**
 * Clear the current route from Breadcrumbs.
 *
 * @return \Rawilk\Breadcrumbs\Breadcrumbs
 */
Breadcrumbs::clearCurrentRoute(): \Rawilk\Breadcrumbs\Breadcrumbs;
</x-code>

### macro
<x-code lang="php">
/**
 * Register a custom macro.
 *
 * @param string $name
 * @param object|callable $macro
 * @return void
 */
Breadcrumbs::macro(string $name, callable $macro): void;
</x-code>
