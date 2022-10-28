---
title: Breadcrumbs Facade
sort: 1
---

### for

```php
/**
 * Register a new breadcrumb.
 *
 * @param string $name
 * @param callable $callback
 * @return void
 */
Breadcrumbs::for(string $name, callable $callback): void;
```

### before

```php
/**
 * Register a breadcrumb to be rendered before every breadcrumb.
 *
 * @param callable $callback
 * @return void
 */
Breadcrumbs::before(callable $callback): void;
```

### exists

```php
/**
 * Check if a breadcrumb exists.
 * Omit name to check if a breadcrumb exists for the current route.
 *
 * @param string|null $name
 * @return bool
 */
Breadcrumbs::exists(string $name = null): bool;
```

### generate

```php
/**
 * Generate breadcrumbs for the given registered breadcrumb, or the current route.
 *
 * @param string|null $name
 * @param ...$params
 * @return \Illuminate\Support\Collection
 */
Breadcrumbs::generate(string $name = null, ...$params): Collection;
```

### view

```php
/**
 * Render a registered breadcrumb using the given view.
 *
 * @param string $view
 * @param string|null $name
 * @param ...$params
 * @return string
 */
Breadcrumbs::view(string $view, string $name = null, ...$params): string;
```

### render

```php
/**
 * Render a registered breadcrumb.
 *
 * @param string|null $name
 * @param ...$params
 * @return string
 */
Breadcrumbs::render(string $name = null, ...$params): string;
```

### current

```php
/**
 * Retrieve the breadcrumb for the current page.
 *
 * @return object|null
 */
Breadcrumbs::current(): ?object;
```

### setCurrentRoute

```php
/**
 * Manually override the current route.
 *
 * @param string $name
 * @param ...$params
 * @return \Rawilk\Breadcrumbs\Breadcrumbs
 */
Breadcrumbs::setCurrentRoute(string $name, ...$params): \Rawilk\Breadcrumbs\Breadcrumbs;
```

### clearCurrentRoute

```php
/**
 * Clear the current route from Breadcrumbs.
 *
 * @return \Rawilk\Breadcrumbs\Breadcrumbs
 */
Breadcrumbs::clearCurrentRoute(): \Rawilk\Breadcrumbs\Breadcrumbs;
```

### macro

```php
/**
 * Register a custom macro.
 *
 * @param string $name
 * @param object|callable $macro
 * @return void
 */
Breadcrumbs::macro(string $name, callable $macro): void;
```
