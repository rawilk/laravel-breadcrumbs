<?php

namespace Rawilk\Breadcrumbs\Support;

use Illuminate\Support\Collection;
use Rawilk\Breadcrumbs\Contracts\Generator as GeneratorContract;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;

class Generator implements GeneratorContract
{
    protected Collection $breadcrumbs;
    protected array $callbacks = [];

    protected function call(string $name, array $params): self
    {
        if (! isset($this->callbacks[$name])) {
            throw new BreadcrumbsNotRegistered($name);
        }

        $this->callbacks[$name]($this, ...$params);

        return $this;
    }

    public function parent(string $name, ...$params): self
    {
        return $this->call($name, $params);
    }

    public function push(string $title, string $url = null, array $data = []): self
    {
        $this->breadcrumbs->push((object) array_merge($data, compact('title', 'url')));

        return $this;
    }
}
