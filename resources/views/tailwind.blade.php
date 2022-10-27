@if (count($breadcrumbs))
<div class="breadcrumbs">
    <nav class="flex" aria-label="Breadcrumbs">
        <ol role="list" class="flex items-center space-x-4">
            @foreach ($breadcrumbs as $breadcrumb)
                <li>
                    <div class="flex items-center">
                        @unless ($loop->first)
                            <span class="flex-shrink-0 text-gray-400 dark:text-gray-300" aria-hidden="true">/</span>
                        @endunless

                        <a href="{{ $breadcrumb->url && ! $loop->last ? $breadcrumb->url : '#' }}"
                           @if ($loop->last) aria-current="page" @endif
                           @class([
                               'text-sm font-medium text-gray-500 dark:text-gray-400 breadcrumb-item',
                               'ml-4' => ! $loop->first,
                               'hover:text-gray-700 dark:hover:text-white' => ! $loop->last,
                               'pointer-events-none breadcrumb-item--active' => $loop->last,
                           ])
                        >
                            {{ $breadcrumb->title }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
</div>
@endif
