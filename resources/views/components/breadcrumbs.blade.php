@props([
    'breadcrumbs' => null,
    'params' => [],
])

<div>
    @if ($breadcrumbs !== false && \Rawilk\Breadcrumbs\Facades\Breadcrumbs::exists($breadcrumbs))
        {!! \Rawilk\Breadcrumbs\Facades\Breadcrumbs::render($breadcrumbs, ...$params) !!}
    @endif
</div>
