---
title: Defining Breadcrumbs
sort: 2
---

## Introduction

Breadcrumbs will usually correspond to actions or types of a page. For each breadcrumb, you specify a name, the breadcrumb title
and the URL to link it to. Since these are likely to change dynamically, you do this in a closure, and pass in any variables you
need into the closure.

## Static Pages
The most simple breadcrumb is probably going to be your homepage, which could look something like this:

```php
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));
```

> {note} In the example above, a [PHP 7.4 arrow function](https://www.php.net/manual/en/functions.arrow.php) is used, but you are free to use regular style closures as well.

> {tip} In the example above, `$trail` is type-hinted to `Rawilk\Breadcrumbs\Support\Generator`, but you are free to use your own generator class if you want (be sure to define it in the config), and you also don't need to type-hint it if you don't want to.

When you call `$trail->push($title, $url)` inside the closure, it adds a breadcrumb link for the page.

For generating a URL, you can use any of the standard Laravel URL-generation methods, including:

- `url('path/to/route')` (`URL::to()`)
- `secure_url('path/to/route')`
- `route('route-name')` or `route('route-name', 'param')` or `route('route-name', ['param1', 'param2'])` (`URL::route()`)
- `action('controller@action')` (`URL::action()`)
- Or just pass a string URL (`http://www.example.com`)

This example would be rendered like this:

```html
{!! Breadcrumbs::render('home') !!}
```

And results in this output:

{.ignore}
> Home

## Parent Links

This is another static page, but it has a parent link before it:

```php
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for('blog', fn (Generator $trail) => $trail->parent('home')->push('Blog', route('blog')));
```

> {note} It works by calling the closure for the `home` breadcrumb defined above via `parent()`.

It would be rendered like this:

```html
{!! Breadcrumbs::render('blog') !!}
```

And results in this output:

{.ignore}
> [Home](#) / Blog

> {note} The default templates do not create a link for the last breadcrumb (the one for the current page), even when a URL is specified.
> You can override this by creating your own template or overriding the package's pre-defined templates. See
> [Custom Templates](/docs/laravel-breadcrumbs/{version}usage/custom-templates) for more details.

## Dynamic Titles and Links

This is a dynamically generated page pulled from the database:

```php
Breadcrumbs::for('post', fn (Generator $trail, $post) => $trail->parent('blog')->push($post->title, route('post', $post)));
```

The `$post` object (usually an [Eloquent](https://laravel.com/docs/7.x/eloquent) model, but could be anything) would simply be passed in from the view:

```html
{!! Breadcrumbs::render('post', $post) !!}
```

The output from this would be:

{.ignore}
> [Home](#) / [Blog](#) / Post Title

> {tip} You can pass in multiple parameters if necessary.

## Nested Categories

If you have nested categories or other special requirements, you can call `$trail->push()` multiple times.

```php
Breadcrumbs::for('category', function (Generator $trail, $category) {
    $trail->parent('blog');

    foreach ($category->parents as $parent) {
        $trail->push($parent->title, route('category', $parent->id));
    }

    $trail->push($category->title, route('category, $category->id));
});
```

Alternatively, you could make a recursive function like this:

```php
Breadcrumbs::for('category', function (Generator $trail, $category) {
    if ($category->parent) {
        $trail->parent('category', $category->parent);
    } else {
        $trail->parent('blog');
    }

    $trail->push($category->title, route('category', $category->slug));
});
```

Both would be rendered like this:

```html
{!! Breadcrumbs::render('category', $category) !!}
```

The result could end up like this:

{.ignore}
> [Home](#) / [Blog](#) / [Grandparent Category](#) / [Parent Category](#) / Category Title
