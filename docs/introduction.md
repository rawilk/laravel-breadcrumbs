---
title: Introduction
sort: 1
---

With Laravel Breadcrumbs you can easily add breadcrumbs to your Laravel applications. This package works very similar to how the
[breadcrumbs package created by James Mills](https://github.com/davejamesmiller/laravel-breadcrumbs) works. I created my own version
of the package because the one by James Mills has been abandoned, and I want to continue to provide this kind of functionality in my
own Laravel apps.

Here's a simple example of how you can define breadcrumbs, and then render them in a view:

```php
// somewhere in a file defined in config/breadcrumbs.php.
// default: 'view' => base_path('routes/breadcrumbs.php')

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;

// Home
Breadcrumbs::for('home', function (Generator $trail) {
    $trail->push('Home', route('home'));
});

// Home > About
Breadcrumbs::for('about', function (Generator $trail) {
    $trail->parent('home')->push('About', route('about'));
});
```

Now in a view somewhere, enter this:

```html
<!-- will render a partial with links for Home > About -->
{{ Breadcrumbs::render('about') }}
```
