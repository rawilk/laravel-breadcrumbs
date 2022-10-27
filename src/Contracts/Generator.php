<?php

namespace Rawilk\Breadcrumbs\Contracts;

use Illuminate\Support\Collection;

interface Generator
{
    /**
     * Generate the registered breadcrumbs.
     *
     * @param  array  $callbacks The registered breadcrumb-generating closures
     * @param  array  $before Any registered "before" callbacks
     * @param  string  $name The name of the current route
     * @param  array  $params Any route parameters
     * @return \Illuminate\Support\Collection
     *
     * @throws \Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered
     */
    public function generate(array $callbacks, array $before, string $name, array $params): Collection;
}
