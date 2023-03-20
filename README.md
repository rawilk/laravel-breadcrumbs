# Breadcrumbs for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawilk/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
![Tests](https://github.com/rawilk/laravel-breadcrumbs/workflows/Tests/badge.svg?style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/rawilk/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/rawilk/laravel-breadcrumbs?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
[![License](https://img.shields.io/github/license/rawilk/laravel-breadcrumbs?style=flat-square)](https://github.com/rawilk/laravel-breadcrumbs/blob/main/LICENSE.md)

![social image](https://banners.beyondco.de/Breadcrumbs%20for%20Laravel.png?theme=light&packageManager=composer+require&packageName=rawilk%2Flaravel-breadcrumbs&pattern=architect&style=style_1&description=Easily+add+breadcrumbs+to+a+Laravel+app.&md=1&showWatermark=0&fontSize=100px&images=chevron-double-right)

With Breadcrumbs for Laravel, you can easily add breadcrumbs to your Laravel applications. This package works very similar to the
[breadcrumbs package created by James Mills](https://github.com/davejamesmiller/laravel-breadcrumbs). I created my own version of the
package because that one has been abandoned, and I want to continue to provide this kind of functionality in my own Laravel apps.

Here's a simple example of how you can define breadcrumbs, and then render them in a view:

```php
// somewhere in a file defined in config/breadcrumbs.php. default = 'view' => base_path('routes/breadcrumbs.php')
Breadcrumbs::for('home', function (Generator $trail) {
    $trail->push('Home', route('home'));
});

// Home > About
Breadcrumbs::for('about', function (Generator $trail)  {
    $trail->parent('home')->push('About', route('about'));
});
```

```html
<!-- will render a view with links for Home > About -->
<nav>{!! Breadcrumbs::render('about') !!}</nav>
```

## Installation

You can install the package via composer:

```bash
composer require rawilk/laravel-breadcrumbs:3.0
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Rawilk\Breadcrumbs\BreadcrumbsServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    /*
     * View name:
     *
     * Choose a view to display when Breadcrumbs::render() is called.
     * Built-in templates are:
     *
     * - 'breadcrumbs::tailwind' - TailwindCSS
     * - 'breadcrumbs::bootstrap4' - Bootstrap 4
     * - 'breadcrumbs::bulma' - Bulma
     * - 'breadcrumbs::json-ld' - JSON-LD Structured Data
     */
    'view' => 'breadcrumbs::tailwind',

    /*
     * Breadcrumb File(s):
     *
     * The file(s) where breadcrumbs are defined. e.g.
     * - base_path('routes/breadcrumbs.php')
     */
    'files' => [
        base_path('routes/breadcrumbs.php'),
    ],

    /*
     * Exceptions:
     *
     * Determine when this package throws exceptions.
     */
    'exceptions' => [
        // Thrown when rendering route-bound breadcrumbs but the current route doesn't have a name.
        'unnamed_route' => true,

        // Thrown when attempting to render breadcrumbs that have not been registered.
        'not_registered' => true,

        // Thrown when attempting to render "route-bound" breadcrumbs and the named route's breadcrumbs are not defined.
        'missing_route_bound_breadcrumb' => true,
    ],

    /*
     * The breadcrumbs class is responsible for registering your breadcrumbs.
     *
     * You are free to extend the package's class, or define your own.
     */
    'breadcrumbs_class' => \Rawilk\Breadcrumbs\Breadcrumbs::class,

    /*
     * The generator class is responsible for generating the breadcrumbs.
     *
     * You are free to extend the package's class, or define your own.
     * If you define your own, it must implement: Rawilk\Breadcrumbs\Contracts\Generator
     */
    'generator_class' => \Rawilk\Breadcrumbs\Support\Generator::class,
];
```

## Documentation
For more documentation, please visit: https://randallwilk.dev/docs/laravel-breadcrumbs/v3

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email randall@randallwilk.dev instead of using the issue tracker.

## Credits

- [Randall Wilk](https://github.com/rawilk)
- [Dave James Miller](https://github.com/davejamesmiller/laravel-breadcrumbs)
- [All Contributors](../../contributors)

## Disclaimer

This package is not affiliated with, maintained, authorized, endorsed or sponsored by Laravel or any of its affiliates.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
