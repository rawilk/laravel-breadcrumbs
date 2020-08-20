---
title: Generator
sort: 2
---

`Rawilk\Breadcrumbs\Support\Generator`

### push
<x-code lang="php">
/**
 * Push a link onto the breadcrumbs stack.
 *
 * @param string $title
 * @param string|null $url
 * @param array $data
 * @return \Rawilk\Breadcrumbs\Support\Generator
 */
public function push(string $title, string $url = null, array $data = []): Generator
</x-code>

### parent
<x-code lang="php">
/**
 * Add an already defined breadcrumb onto the current breadcrumb stack.
 *
 * @param string $name
 * @param ...$params
 * @return \Rawilk\Breadcrumbs\Support\Breadcrumbs
 */
public function parent(string $name, ...$params): Generator
</x-code>
