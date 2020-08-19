@if (count($breadcrumbs))
<div class="breadcrumbs">
    <nav class="sm:hidden">
        <a href="{{ url()->previous() }}"
           class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out breadcrumb-item"
        >
            <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>

            {{ __('Back') }}
        </a>
    </nav>

    <nav class="hidden sm:flex items-center text-sm leading-5 font-medium"
         aria-label="breadcrumbs"
    >
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && ! $loop->last)
                <a href="{{ $breadcrumb->url }}"
                   class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out breadcrumb-item"
                >
                    {{ $breadcrumb->title }}
                </a>
            @else
                <a href="#" class="text-gray-700 m-0 pointer-events-none breadcrumb-item--active" aria-current="page">
                    {{ $breadcrumb->title }}
                </a>
            @endif


            @if (! $loop->last)
                <svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            @endif
        @endforeach
    </nav>
</div>
@endif
