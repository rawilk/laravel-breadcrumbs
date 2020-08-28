---
title: Using Multiple Files
sort: 5
---

If you don't want to use `routes/breadcrumbs.php`, or want to use multiple files, you can change it in the `config/breadcrumbs.php` file.

```php
'files' => [base_path('routes/breadcrumbs.php')],
```

### Absolute Paths
You can define an array of absolute paths:

```php
'files' => [
    base_path('breadcrumbs/admin.php'),
    base_path('breadcrumbs/frontend.php'),
],
```

### Glob
You can also use `glob()` to automatically find files using a wildcard.

```php
'files' => [glob(base_path('breadcrumbs/*.php)],
```

Or return an empty array `[]` to disable loading.
