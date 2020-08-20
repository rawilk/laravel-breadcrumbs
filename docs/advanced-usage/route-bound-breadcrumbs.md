---
title: Route-Bound Breadcrumbs
sort: 2
---

In normal usage you must call `Breadcrumbs::render($name, ...$params)` to render the breadcrumbs on every page.
If you prefer, you can name your breadcrumbs the same as you name your routes and avoid this duplication.

## Name Your Routes

Make sure each of your routes has a name. For example (`routes/web.php`):

<x-code lang="php">
@verbatim
// Home
Route::get('/', 'HomeController@index')->name('home');

// Home > [Post]
Route::get('/post/{id}', 'PostsController@show')->name('post');
@endverbatim
</x-code>

For more details see [Named Routes](https://laravel.com/docs/7.x/routing#named-routes) in the Laravel documentation.

## Name Your Breadcrumbs to Match

For each route, create a breadcrumb with the same name and parameters. For example (`routes/breadcrumbs.php`):

<x-code lang="php">
// Home
Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

// Home > [Post]
Breadcrumbs::for('post', function (Generator $trail, $id) {
    $post = Post::findOrFail($id);
    $trail->parent('home')->push($post->title, route('post', $post));
});
</x-code>

To add breadcrumbs to a [custom 404 Not Found page](https://laravel.com/docs/7.x/errors#custom-http-error-pages), use the name `errors.404`:

<x-code lang="php">
// Error 404
Breadcrumbs::for('errors.404', fn (Generator $trail) => $trail->parent('home')->push('Page Not Found'));
</x-code>

## Output Breadcrumbs in your Layout

Call `Breadcrumbs::render()` with no parameters in your layout file (e.g. `resources/views/app.blade.php`):

<x-code lang="html">@verbatim{{ Breadcrumbs::render() }}@endverbatim</x-code>

This will automatically output breadcrumbs corresponding to the current route. The same applies to `Breadcrumbs::generate()`:

<x-code lang="php">$breadcrumbs = Breadcrumbs::generate();</x-code>

And for `Breadcrumbs::view()`:

<x-code lang="html">@verbatim{{ Breadcrumbs::view('breadcrumbs::json-ld') }}@endverbatim</x-code>

## Route Binding Exceptions

If you try to render a breadcrumb that doesn't exist, the package will throw a `BreadcrumbsNotRegistered` Exception to remind you to create one.
You can disable this (e.g. if you have some pages with no breadcrumbs) in the `config/breadcrumbs.php` file:

<x-code lang="php">
'exceptions' => [
    ...
    'missing_route_bound_breadcrumb' => false,
],
</x-code>

Similarly, to prevent it from throwing an `UnnamedRoute` Exception if the current route doesn't have a name, set this value:

<x-code lang="php">
'exceptions' => [
    'unnamed_route' => false,
    ...
],
</x-code>

## Route Model Binding
Laravel Breadcrumbs uses the same binding as the controller. For example:

<x-code lang="php">
// routes/web.php
Route::get('/post/{post}', 'PostsController@show')->name('post');
</x-code>

<x-code lang="php">
// app/Http/Controllers/PostsController.php
use App\Post;

class PostsController extends Controller
{
    public function show(Post $post) // <-- Implicit model binding happens here
    {
        return view('posts.show', compact('post'));
    }
}
</x-code>

<x-code lang="php">
// routes/breadcrumbs.php
Breadcrumbs::for('post', function (Generator $trail, $post) { // <-- The same Post model is injected here
    $trail->parent('home')
        ->push($post->title, route('post', $post));
});
</x-code>

Using route model binding makes your code less verbose and more efficient by only loading the post from the database once.
You can optionally type-hint the `$post` parameter for clarity if you want to.

For more details see [Route Model Binding](https://laravel.com/docs/7.x/routing#route-model-binding) in the Laravel documentation.
