---
title: Custom Classes
sort: 9
---

For more advanced customizations, you may subclass `Rawilk\Breadcrumbs\Breadcrumbs` and/or `Rawilk\Breadcrumbs\Support\Generator`, and then
update the `config/breadcrumbs.php` file with the new class name.

```php
'breadcrumbs_class' => \Rawilk\Breadcrumbs\Breadcrumbs::class,

'generator_class' => \Rawilk\Breadcrumbs\Support\Generator::class,
```

>{.tip} **Note:** Anything that's not part of the public API may change between releases, so I suggest you write unit tests to ensure it doesn't break when upgrading.

If you choose to use your own `generator_class`, your class must implement the `Rawilk\Breadcrumbs\Contracts\Generator` contract. Here is what the contract
looks like:

```php
namespace Rawilk\Breadcrumbs\Contracts;

use Illuminate\Support\Collection;

interface Generator
{
    /**
     * Generate the registered breadcrumbs.
     *
     * @param array $callbacks The registered breadcrumb-generating closures
     * @param array $before Any registered "before" callbacks
     * @param string $name The name of the current route
     * @param array $params Any route parameters
     * @return \Illuminate\Support\Collection
     * @throws \Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered
     */
    public function generate(array $callbacks, array $before, string $name, array $params): Collection;
}
```
