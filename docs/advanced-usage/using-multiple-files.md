---
title: Using Multiple Files
sort: 5
---

If you don't want to use `routes/breadcrumbs.php`, or want to use multiple files, you can change it in the `config/breadcrumbs.php` file.

<x-code lang="php">'files' => [base_path('routes/breadcrumbs.php')],</x-code>

### Absolute Paths
You can define an array of absolute paths:

<x-code lang="php">
'files' => [
    base_path('breadcrumbs/admin.php'),
    base_path('breadcrumbs/frontend.php'),
],
</x-code>

### Glob
You can also use `glob()` to automatically find files using a wildcard.

<x-code lang="php">'files' => [glob(base_path('breadcrumbs/*.php)],</x-code>

Or return an empty array `[]` to disable loading.
