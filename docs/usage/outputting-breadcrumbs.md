---
title: Outputting Breadcrumbs
sort: 4
---

Call `Breadcrumbs::render()` in the view for each page, passing it the name of the breadcrumb to use an any additional parameters.

## With Blade

In the page (e.g. `resources/views/home.blade.php`):

<x-code lang="html">@verbatim{{ Breadcrumbs::render('home') }}@endverbatim</x-code>

Or with a parameter:

<x-code lang="html">@verbatim{{ Breadcrumbs::render('category', $category) }}@endverbatim</x-code>

## With Blade Layouts and @@section

In the page (e.g. `resources/views/home.blade.php`):

<x-code lang="html">
@verbatim
@extends('layouts.master')

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection
@endverbatim
</x-code>

Or using the shorthand syntax:

<x-code lang="html">
@verbatim
@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('home'))
@endverbatim
</x-code>

And in the layout file (e.g. `resources/views/layouts/master.blade.php`):

<x-code lang="html">@verbatim@yield('breadcrumbs')@endverbatim</x-code>

## Pure PHP (without Blade)
In the page (e.g. `resources/views/home.php`):

<x-code lang="php">{!! '<?php' !!} echo Breadcrumbs::render('home'); ?></x-code>

## Structured Data

To render breadcrumbs as JSON-LD [structured data](https://developers.google.com/search/docs/data-types/breadcrumbs) (usually for SEO reasons),
use `Breadcrumbs::view()` to render the `breadcrumbs::json-ld` template in addition to the normal one. For example:

<x-code lang="html">
@verbatim
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
@endverbatim
</x-code>

<x-tip><strong>Note:</strong> If you use <a href="https://github.com/renatomarinho/laravel-page-speed">Laravel Page Speed</a> you may need to <a href="https://github.com/renatomarinho/laravel-page-speed/issues/66">disable the <code>TrimUrls</code> middleware</a>.</x-tip>

To specify an image, add it to the `$data` parameter in `push()`:

<x-code lang="php">
Breadcrumbs::for('post', function (Generator $trail, $post) {
    $trail->parent('home')
        ->push($post->title, route('post', $post), ['image' => asset($post->image)]);
});
</x-code>

If you prefer to use Microdata or RDFa, you will need to create a [custom template](/laravel-breadcrumbs/v1/usage/custom-templates).
