<option value="{{ $node->slug }}" @if (request('category') == $node->slug) selected @endif>
    {!! str_repeat('&nbsp;&nbsp;', $depth ?? 0) !!}{{ $node->name }}
</option>

@if ($node->childrenRecursive->isNotEmpty())
    @foreach ($node->childrenRecursive as $child)
        @include('partial.category-node', [
            'node' => $child,
            'depth' => ($depth ?? 0) + 1,
        ])
    @endforeach
@endif
