# Breadcrumbs for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawilk/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
![Tests](https://github.com/rawilk/laravel-breadcrumbs/workflows/Tests/badge.svg?style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/rawilk/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/rawilk/laravel-breadcrumbs?style=flat-square)](https://packagist.org/packages/rawilk/laravel-breadcrumbs)
[![License](https://img.shields.io/github/license/rawilk/laravel-breadcrumbs?style=flat-square)](https://github.com/rawilk/laravel-breadcrumbs/blob/main/LICENSE.md)

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
composer require rawilk/laravel-breadcrumbs
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="breadcrumbs-config"
```

You can view the default configuration here: https://github.com/rawilk/laravel-breadcrumbs/blob/main/config/breadcrumbs.php

## Documentation

For more documentation, please visit: https://randallwilk.dev/docs/laravel-breadcrumbs

## Testing

On a fresh install, run the setup bin script first, otherwise certain DOM assertions won't work.

```bash
./bin/setup.sh
```

For convenience, a composer script is setup to run the pest test suite in parallel.

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email randall@randallwilk.dev instead of using the issue tracker.

## Credits

-   [Randall Wilk](https://github.com/rawilk)
-   [Dave James Miller](https://github.com/davejamesmiller/laravel-breadcrumbs)
-   [All Contributors](../../contributors)

## Disclaimer

This package is not affiliated with, maintained, authorized, endorsed or sponsored by Laravel or any of its affiliates.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
