---
title: Outputting Breadcrumbs
sort: 4
---

## Introduction

Call `Breadcrumbs::render()` in the view for each page, passing it the name of the breadcrumb to use an any additional parameters.

## With Blade

In the page (e.g. `resources/views/home.blade.php`):

```html
{{ Breadcrumbs::render('home') }}
```

Or with a parameter:

```html
{{ Breadcrumbs::render('category', $category) }}
```

## With Blade Layouts and @section

In the page (e.g. `resources/views/home.blade.php`):

```html
@extends('layouts.master')

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection
```

Or using the shorthand syntax:

```html
@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('home'))
```

And in the layout file (e.g. `resources/views/layouts/master.blade.php`):

```html
@yield('breadcrumbs')
```

## Pure PHP (without Blade)
In the page (e.g. `resources/views/home.php`):

```php
<?php echo Breadcrumbs::render('home'); ?>
```

## Structured Data

To render breadcrumbs as JSON-LD [structured data](https://developers.google.com/search/docs/data-types/breadcrumbs) (usually for SEO reasons),
use `Breadcrumbs::view()` to render the `breadcrumbs::json-ld` template in addition to the normal one. For example:

```html
<html>
    <head>
        ...
        {{ Breadcrumbs::view('breadcrumbs::json-ld', 'category', $category) }}
        ...
    </head>
    <body>
        ...
        {{ Breadcrumbs::render('category', $category) }}
        ...
    </body>
</html>
```

{.tip}
> **Note:** If you use [Laravel Page Speed](https://github.com/renatomarinho/laravel-page-speed) you may need to [disable the `TrimUrls` middleware](https://github.com/renatomarinho/laravel-page-speed/issues/66).

To specify an image, add it to the `$data` parameter in `push()`:

```php
Breadcrumbs::for('post', function (Generator $trail, $post) {
    $trail->parent('home')
        ->push($post->title, route('post', $post), ['image' => asset($post->image)]);
});
```

If you prefer to use Microdata or RDFa, you will need to create a [custom template](/docs/laravel-breadcrumbs/v1/usage/custom-templates).

## Blade Component

As of version `2.0.0`, you can use the provided `<x-breadcrumbs />` blade component to render
your breadcrumbs. By default, it uses the current route name to determine if there are breadcrumbs
that exist to render. You can manually specify which breadcrumbs you want rendered as well:

```html
<x-breadcrumbs breadcrumbs="your.breadcrumbs.route_name" />

<!-- with parameters -->
<x-breadcrumbs breadcrumbs="your.breadcrumbs.route_name" :params="[$user]" />
```

You can also pass in `false` as the `breadcrumbs` attribute if you need to disable the rendering of
the breadcrumbs altogether:

```html
<x-breadcrumbs :breadcrumbs="false" />
```
