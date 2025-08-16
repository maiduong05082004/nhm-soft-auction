@if ($paginator->hasPages())
    <div class="join">

        @if ($paginator->onFirstPage())
            <button class="join-item btn btn-disabled">«</button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="join-item btn">«</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <button class="join-item btn btn-disabled">{{ $element }}</button>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="join-item btn btn-active">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="join-item btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="join-item btn">»</a>
        @else
            <button class="join-item btn btn-disabled">»</button>
        @endif
    </div>
@endif
