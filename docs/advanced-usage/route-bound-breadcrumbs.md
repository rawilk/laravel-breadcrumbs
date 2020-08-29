---
title: Route-Bound Breadcrumbs
sort: 2
---

## Introduction

In normal usage you must call `Breadcrumbs::render($name, ...$params)` to render the breadcrumbs on every page.
If you prefer, you can name your breadcrumbs the same as you name your routes and avoid this duplication.

## Name Your Routes

Make sure each of your routes has a name. For example (`routes/web.php`):

```php
// Home
Route::get('/', 'HomeController@index')->name('home');

// Home > [Post]
Route::get('/post/{id}', 'PostsController@show')->name('post');
```

For more details see [Named Routes](https://laravel.com/docs/7.x/routing#named-routes) in the Laravel documentation.

## Name Your Breadcrumbs to Match

For each route, create a breadcrumb with the same name and parameters. For example (`routes/breadcrumbs.php`):

```php
// Home
Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', route('home')));

// Home > [Post]
Breadcrumbs::for('post', function (Generator $trail, $id) {
    $post = Post::findOrFail($id);
    $trail->parent('home')->push($post->title, route('post', $post));
});
```

To add breadcrumbs to a [custom 404 Not Found page](https://laravel.com/docs/7.x/errors#custom-http-error-pages), use the name `errors.404`:

```php
// Error 404
Breadcrumbs::for('errors.404', fn (Generator $trail) => $trail->parent('home')->push('Page Not Found'));
```

## Output Breadcrumbs in your Layout

Call `Breadcrumbs::render()` with no parameters in your layout file (e.g. `resources/views/app.blade.php`):

```html
{{ Breadcrumbs::render() }}
```

This will automatically output breadcrumbs corresponding to the current route. The same applies to `Breadcrumbs::generate()`:

```php
$breadcrumbs = Breadcrumbs::generate();
```

And for `Breadcrumbs::view()`:

```html
{{ Breadcrumbs::view('breadcrumbs::json-ld') }}
```

## Route Binding Exceptions

If you try to render a breadcrumb that doesn't exist, the package will throw a `BreadcrumbsNotRegistered` Exception to remind you to create one.
You can disable this (e.g. if you have some pages with no breadcrumbs) in the `config/breadcrumbs.php` file:

```php
'exceptions' => [
    ...
    'missing_route_bound_breadcrumb' => false,
],
```

Similarly, to prevent it from throwing an `UnnamedRoute` Exception if the current route doesn't have a name, set this value:

```php
'exceptions' => [
    'unnamed_route' => false,
    ...
],
```

## Route Model Binding
Laravel Breadcrumbs uses the same binding as the controller. For example:

```php
// routes/web.php
Route::get('/post/{post}', 'PostsController@show')->name('post');
```

```php
// app/Http/Controllers/PostsController.php
use App\Post;

class PostsController extends Controller
{
    public function show(Post $post) // <-- Implicit model binding happens here
    {
        return view('posts.show', compact('post'));
    }
}
```

```php
// routes/breadcrumbs.php
Breadcrumbs::for('post', function (Generator $trail, $post) { // <-- The same Post model is injected here
    $trail->parent('home')
        ->push($post->title, route('post', $post));
});
```

Using route model binding makes your code less verbose and more efficient by only loading the post from the database once.
You can optionally type-hint the `$post` parameter for clarity if you want to.

For more details see [Route Model Binding](https://laravel.com/docs/7.x/routing#route-model-binding) in the Laravel documentation.
