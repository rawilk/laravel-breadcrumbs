---
title: Dependency Injection
sort: 6
---

You can use [dependency injection](https://laravel.com/docs/7.x/providers#the-boot-method) to access the `Breadcrumbs` instance if you prefer, instead of using the `Breadcrumbs::` facade:

```php
use Rawilk\Breadcrumbs\Breadcrumbs;
use Illuminate\Support\ServiceProvider;

class MyServiceProvider extends ServiceProvider
{
    public function boot(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->for(...);
    }
}
```
