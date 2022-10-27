@if ($breadcrumbs)
    <ul class="custom-view">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->last)
                <li class="current">{{ $breadcrumb->title }}</li>
            @elseif ($breadcrumb->url)
                <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @else
                <li>{{ $breadcrumb->title }}</li>
            @endif
        @endforeach
    </ul>
@endif
