<?php

namespace Rawilk\Breadcrumbs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void for(string $name, callable $callback)
 * @method static void before(callable $callback)
 * @method static bool exists(string $name = null)
 * @method static \Illuminate\Support\Collection generate(string $name = null, ...$params)
 * @method static string view(string $view, string $name = null, ...$params)
 * @method static string render(string $name = null, ...$params)
 * @method static object|null current()
 * @method static \Rawilk\Breadcrumbs\Breadcrumbs setCurrentRoute(string $name, ...$params)
 * @method static \Rawilk\Breadcrumbs\Breadcrumbs clearCurrentRoute()
 * @see \Rawilk\Breadcrumbs\Breadcrumbs
 * @mixin \Illuminate\Support\Traits\Macroable
 */
class Breadcrumbs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Rawilk\Breadcrumbs\Breadcrumbs::class;
    }
}
