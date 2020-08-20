---
title: Custom Templates
sort: 3
---

## Create a View

To customize the HTML, create your own view file (e.g. `resources/views/partials/breadcrumbs.blade.php`), like this:

<x-code lang="html">
@verbatim
@if (count($breadcrumbs))

    <ol class="breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && ! $loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                </li>
            @else
                <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li>
            @endif

        @endforeach
    </ol>

@endif
@endverbatim
</x-code>

<x-tip>See the <a href="https://github.com/rawilk/laravel-breadcrumbs/tree/master/resources/views">views/ directory</a> for the built-in templates.</x-tip>

### View Data

The view will receive an object called `$breadcrumbs`.

Each breadcrumb is an object with the following keys:

- `title` - The breadcrumb title
- `url` - The breadcrumb URL, or `null` if none was given
- Any additional keys for each item in `$data` (see [Custom Data](/laravel-breadcrumbs/v1/advanced-usage/advanced-usage#custom-data))

## Update the Config

When you create a custom view, you need to update the `config/breadcrumbs.php` file with the custom view name:

<x-code lang="php">
'view' => 'partials.breadcrumbs', #--> resources/views/partials/breadcrumbs.blade.php
</x-code>

## Override Template Views

Instead of creating your own custom view, you may choose to override one the package's pre-defined templates. You can do so by publishing the views:

<x-code lang="bash">
php artisan vendor:publish --provider="Rawilk\Breadcrumbs\BreadcrumbsServiceProvider" --tag="views"
</x-code>

This will publish the views to `resources/views/vendor/breadcrumbs`. From there, you can modify the views to fit your needs.
