---
title: Macros
sort: 7
---

The Breadcrumbs instance is [macroable](https://unnikked.ga/understanding-the-laravel-macroable-trait-dab051f09172), so you can add your own methods. For example:

```php
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::macro('pageTitle', function () {
    $title = ($breadcrumb = $this->current()) ? "{$breadcrumb->title} - " : '';

    if (($page = (int) request('page')) > 1) {
        $title .= "Page {$page} - ";
    }

    return $title . 'Acme';
});
```

```html
<title>{!! Breadcrumbs::pageTitle() !!}</title>
```
