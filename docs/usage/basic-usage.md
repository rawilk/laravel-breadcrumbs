---
title: Basic Usage
sort: 1
---

## Define Your Breadcrumbs

Create a file called `routes/breadcrumbs.php`:

```php
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

// Home
Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

// Home > About
Breadcrumbs::for(
    'about',
    fn (Generator $trail) => $trail->parent('home')->push('About', route('About'))
);

// Home > Blog
Breadcrumbs::for(
    'blog',
    fn (Generator $trail) => $trail->parent('home')->push('Blog', route('blog'))
);

// Home > Blog > [Category]
Breadcrumbs::for(
    'category',
    fn (Generator $trail, $category) => $trail->parent('blog')->push($category->title, route('category', $category->id))
);

// Home > Blog > [Category] > [Post]
Breadcrumbs::for(
    'post',
    fn (Generator $trail, $post) => $trail->parent('category', $post->category)->push($post->title, route('post', $post->id))
);
```

> {tip} See [Defining Breadcrumbs](/docs/laravel-breadcrumbs/{version}usage/defining-breadcrumbs) for more details.

## Choose a Template

By default a [TailwindCSS](https://tailwindui.com/components/application-ui/headings/page-headings#component-8a687a46760e105177d4a4ed39ae6d27)-compatible view will be rendered, so if you're using TailwindCSS you can skip this step.
**Note:** this version of the tailwind template is utilizing classes from [Tailwind UI](https://tailwindui.com/), so if you don't have that included in your application,
you may need to adjust the template or use a custom one.

In the `config/breadcrumbs.php` file, edit this line:

```php
'view' => 'breadcrumbs::tailwind',
```

Other predefined templates include:

- `breadcrumbs::bootstrap4` - [Bootstrap 4](https://getbootstrap.com/docs/4.0/components/breadcrumb/)
- `breadcrumbs::bulma` - [Bulma](https://bulma.io/documentation/components/breadcrumb/)
- `breadcrumbs::json-ld` - [JSON-LD Structured Data](https://developers.google.com/search/docs/data-types/breadcrumbs) (`<script />` tag, no visible output)
- The path to a custom view: e.g. `partials.breadcrumbs`

> {tip} See [Custom Templates](/docs/laravel-breadcrumbs/{version}usage/custom-templates) for more details.

## Render the Breadcrumbs

Finally, call `Breadcrumbs::render()` in the view for each page, passing it the name of the breadcrumb to use and any additional parameters you need.

```html
{!! Breadcrumbs::render('home') !!}

{!! Breadcrumbs::render('category', $category) !!}
```

> {tip} See [Outputting Breadcrumbs](/docs/laravel-breadcrumbs/{version}/usage/outputting-breadcrumbs) for other output options, and see
> [Route-Bound Breadcrumbs](/docs/laravel-breadcrumbs/{version}advanced-usage/route-bound-breadcrumbs) for a way to link
> breadcrumb names to route names automatically.
