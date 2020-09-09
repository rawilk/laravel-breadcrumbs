<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Support;

use Illuminate\Support\Collection;
use Rawilk\Breadcrumbs\Contracts\Generator as GeneratorContract;
use Rawilk\Breadcrumbs\Exceptions\BreadcrumbsNotRegistered;

class Generator implements GeneratorContract
{
    protected Collection $breadcrumbs;
    protected array $callbacks = [];

    public function generate(array $callbacks, array $before, string $name, array $params): Collection
    {
        $this->breadcrumbs = new Collection;
        $this->callbacks = $callbacks;

        foreach ($before as $callback) {
            $callback($this);
        }

        $this->call($name, $params);

        return $this->breadcrumbs;
    }

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
