---
title: Defining Breadcrumbs
sort: 2
---

Breadcrumbs will usually correspond to actions or types of a page. For each breadcrumb, you specify a name, the breadcrumb title
and the URL to link it to. Since these are likely to change dynamically, you do this in a closure, and pass in any variables you
need into the closure.

## Static Pages
The most simple breadcrumb is probably going to be your homepage, which could look something like this:

<x-code lang="php">
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));
</x-code>

<x-tip>
In the example above, a <a href="https://www.php.net/manual/en/functions.arrow.php" target="_blank" rel="noopener">PHP 7.4 arrow function</a>
is used, but you are free to use regular style closures as well.
</x-tip>

<x-tip>
In the example above, <code>$trail</code> is type-hinted to <code>Rawilk\Breadcrumbs\Support\Generator</code>, but you are free to use
your own generator class if you want (be sure to define it in the config), and you also don't need to type-hint it if you don't want to.
</x-tip>

When you call `$trail->push($title, $url)` inside the closure, it adds a breadcrumb link for the page.

For generating a URL, you can use any of the standard Laravel URL-generation methods, including:

- `url('path/to/route')` (`URL::to()`)
- `secure_url('path/to/route')`
- `route('route-name')` or `route('route-name', 'param')` or `route('route-name', ['param1', 'param2'])` (`URL::route()`)
- `action('controller@action')` (`URL::action()`)
- Or just pass a string URL (`http://www.example.com`)

This example would be rendered like this:

<x-code lang="html">@verbatim{{ Breadcrumbs::render('home') }}@endverbatim</x-code>

And results in this output:

> Home

## Parent Links

This is another static page, but it has a parent link before it:

<x-code lang="php">
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

Breadcrumbs::for('blog', fn (Generator $trail) => $trail->parent('home')->push('Blog', route('blog')));
</x-code>

<x-tip>
It works by calling the closure for the <code>home</code> breadcrumb defined above via <code>parent()</code>.
</x-tip>

It would be rendered like this:

<x-code lang="html">@verbatim{{ Breadcrumbs::render('blog') }}@endverbatim</x-code>

And results in this output:

> [Home](#) / Blog

<x-tip>
<strong>Note:</strong> The default templates do not create a link for the last breadcrumb (the one for the current page), even when a URL is specified.
You can override this by creating your own template or overriding the package's pre-defined templates. See
<a href="/laravel-breadcrumbs/v1/usage/custom-templates">Custom Templates</a> for more details.
</x-tip>

## Dynamic Titles and Links

This is a dynamically generated page pulled from the database:

<x-code lang="php">
Breadcrumbs::for('post', fn (Generator $trail, $post) => $trail->parent('blog')->push($post->title, route('post', $post)));
</x-code>

The `$post` object (usually an [Eloquent](https://laravel.com/docs/7.x/eloquent) model, but could be anything) would simply be passed in from the view:

<x-code lang="html">@verbatim{{ Breadcrumbs::render('post', $post) }}@endverbatim</x-code>

The output from this would be:

> [Home](#) / [Blog](#) / Post Title

<x-tip><strong>Tip:</strong> You can pass in multiple parameters if necessary.</x-tip>

## Nested Categories

If you have nested categories or other special requirements, you can call `$trail->push()` multiple times.

<x-code lang="php">
Breadcrumbs::for('category', function (Generator $trail, $category) {
    $trail->parent('blog');

    foreach ($category->parents as $parent) {
        $trail->push($parent->title, route('category', $parent->id));
    }

    $trail->push($category->title, route('category, $category->id));
});
</x-code>

Alternatively, you could make a recursive function like this:

<x-code lang="php">
Breadcrumbs::for('category', function (Generator $trail, $category) {
    if ($category->parent) {
        $trail->parent('category', $category->parent);
    } else {
        $trail->parent('blog');
    }

    $trail->push($category->title, route('category', $category->slug));
});
</x-code>

Both would be rendered like this:

<x-code lang="html">@verbatim{{ Breadcrumbs::render('category', $category) }}@endverbatim</x-code>

The result could end up like this:

> [Home](#) / [Blog](#) / [Grandparent Category](#) / [Parent Category](#) / Category Title
