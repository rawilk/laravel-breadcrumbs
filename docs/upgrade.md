---
title: Upgrade Guide
sort: 5
---

## Upgrading from v3 to v4

### Laravel

v4 of laravel-breadcrumbs now requires a minimum Laravel version of `9.0`. Be sure to update your project to at least that version.

### PHP

v4 of laravel-breadcrumbs now requires a minimum PHP version of `8.1`. Be sure your environment is running at least that version.

### Tailwind Template

v4 of laravel-breadcrumbs changed the default markup of the Tailwind breadcrumbs template. If you rely on the default markup and it does not suit your needs anymore, you may override it by publishing the tailwind blade partial. The following command can be run to publish the views:

```bash
php artisan vendor:publish --provider="Rawilk\Breadcrumbs\BreadcrumbsServiceProvider" --tag="views"
```
