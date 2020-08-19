@if (count($breadcrumbs))
<nav class="breadcrumb" aria-label="breadcrumbs">
    <ul>
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->last)
                <li class="is-active">
                    <a href="{{ $breadcrumb->url ?? '#' }}" aria-current="page">
                        {{ $breadcrumb->title }}
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ $breadcrumb->url ?? '#' }}">
                        {{ $breadcrumb->title }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>
@endif
