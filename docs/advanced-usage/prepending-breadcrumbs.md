---
title: Prepending Breadcrumbs
sort: 3
---

You can register a "before" callback to add breadcrumbs at the start of the trail. For example, to
automatically add the "home" page to the beginning.

<x-code lang="php">
Breadcrumbs::before(fn (Generator $trail) => $trail->push('Home', route('home')));
</x-code>

If you are going to prepend a breadcrumb onto every page, a good place to put the breadcrumb definition would be
in a `boot()` method in a service provider.
