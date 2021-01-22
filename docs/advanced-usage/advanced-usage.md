---
title: Advanced Usage
sort: 1
---

## Breadcrumbs with no URL

The second parameter to `push()` is optional, so if you want a breadcrumb with no URL, you can do so:

```php
$trail->push('Sample');
```

The `$breadcrumb->url` value will be `null`.

The default TailwindCSS templates provided render this with a CSS class of `breadcrumb-item--active`.

## Custom Data

The `push()` method accepts an optional third parameter, `$data` - an array of arbitrary data to be passed to the breadcrumb,
which you can use in your custom template. For example, if you wanted each breadcrumb to have an icon, you could do:

```php
$trail->push('Home', '/', ['icon' => 'home.png']);
```

The `$data` array's entries will be merged into the breadcrumb as properties, so you would access the icon as `$breadcrumb->icon` in your
template, like this:

```html
<li>
    <a href="{{ $breadcrumb->url }}">
        <img src="/images/icons/{{ $breadcrumb->icon }}">
        {{ $breadcrumb->title }}
    </a>
</li>
```

> {note} Do not use keys like `title` or `url` as they will be overwritten.

## Getting the Current Page Breadcrumb

To get the last breadcrumb for the current page, use `Breadcrumb::current()`. For example, you could use this to
output the current page title:

```html
<title>{{ ($breadcrumb = Breadcrumb::current()) ? $breadcrumb->title : 'Fallback Title'  }}</title>
```

To ignore a breadcrumb, add `'current' => false` ot the `$data` parameter in `push()`. This can be useful to ignore pagination breadcrumbs.

```php
Breadcrumbs::for('post', function (Generator $trail, Post $post) {
    $trail->push($post->title, route('post', $post));

    $page = (int) request('page', 1);
    if ($page > 1) {
        $trail->push("Page {$page}", null, ['current' => false]);
    }
});
```

```html
<title>
    {{ ($breadcrumb = Breadcrumbs::current()) ? "{$breadcrumb->title} -" : '' }}
    {{ ($page = (int) request('page')) > 1 ? "Page {$page} -" : '' }}
    Acme
</title>
```

## Switching Views at Runtime
You can use `Breadcrumbs::view()` in place of `Breadcrumbs::render()` to render a template other than the [default one](/docs/laravel-breadcrumbs/v2/usage/basic-usage#choose-a-template):

```html
{{ Breadcrumbs::view('partials.breadcrumbs2', 'category', $category) }}
```

Or you can override the config setting to affect all future `render()` calls:

```php
Config::set('breadcrumbs.view', 'partials.breadcrumbs2');
```

```html
{{ Breadcrumbs::render('category', $category) }}
```

Or you could call `Breadcrumbs::generate()` to get the breadcrumbs collection and load the view manually:

```html
@include('partials.breadcrumbs2', ['breadcrumbs' => Breadcrumbs::generate('category', $category)])
```

## Overriding the "Current" Route

If you call `Breadcrumbs::render()` or `Breadcrumbs::generate()` with no parameters, it will use the current
name and parameters by default (as returned by Laravel's `Route::current()` method).

You can override this by calling `Breadcrumbs::setCurrentRoute($name, $param1, $param2, ...)`.

## Checking if a Breadcrumb Exists

To check if a breadcrumb with a given name exists, you can call `Breadcrumb::exists('name')`, which returns a boolean.
You can also just call `Breadcrumb::exists()` without any parameters to check if a breadcrumb exists for the current route.
