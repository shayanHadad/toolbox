@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="صفحه‌بندی">

        @if ($paginator->onFirstPage())
            <span class="page-link page-disabled">قبلی</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link">قبلی</a>
        @endif

        @php
            $onEachSide = 1;
            $start = max(1, $paginator->currentPage() - $onEachSide);
            $end   = min($paginator->lastPage(), $paginator->currentPage() + $onEachSide);
        @endphp

        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}" class="page-link">1</a>
            @if ($start > 2)
                <span class="page-link page-dots">…</span>
            @endif
        @endif

        @foreach ($paginator->getUrlRange($start, $end) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="page-link page-active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
            @endif
        @endforeach

        @if ($end < $paginator->lastPage())
            @if ($end < $paginator->lastPage() - 1)
                <span class="page-link page-dots">…</span>
            @endif
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link">{{ $paginator->lastPage() }}</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link">بعدی</a>
        @else
            <span class="page-link page-disabled">بعدی</span>
        @endif

    </nav>
@endif
