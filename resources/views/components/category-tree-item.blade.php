@php
    $rawSelected = request()->input('category_ids', request('category_id'));
    $selectedIds = [];

    if ($rawSelected) {
        if (is_array($rawSelected)) {
            $selectedIds = array_map('strval', $rawSelected);
        } else {
            $selectedIds = array_filter(array_map('trim', explode(',', (string) $rawSelected)));
            $selectedIds = array_map('strval', $selectedIds);
        }
    }

    $currentIdStr = (string) $category->id;
    $isChecked = in_array($currentIdStr, $selectedIds, true);

    $open = $isChecked;

    if (!$open && !empty($selectedIds)) {
        if ($category->relationLoaded('childrenRecursive')) {
            $descIds = $category->childrenRecursive
                ->pluck('id')
                ->map(function ($i) {
                    return (string) $i;
                })
                ->toArray();
        } 

        if (!empty($descIds) && count(array_intersect($selectedIds, $descIds)) > 0) {
            $open = true;
        }
    }
@endphp

<div class="category-item" data-category-id="{{ $category->id }}" data-full-path="{{ $category->full_path }}">
    <div class="flex items-center py-1 px-2 hover:bg-white rounded-md group transition-colors">

        @if ($category->children->count() > 0)
            <button type="button" class="category-toggle mr-1 p-1 hover:bg-gray-200 rounded transition-colors"
                aria-label="Toggle children">
                <x-heroicon-o-chevron-right class="h-3 w-3 text-gray-500 transition-transform" />
            </button>
        @else
            <div class="w-5"></div>
        @endif

        <label class="flex items-center flex-1 cursor-pointer py-1">
            <input type="checkbox" value="{{ $category->id }}" data-full-path="{{ $category->full_path }}"
                class="category-checkbox checkbox checkbox-xs checkbox-primary mr-2 flex-shrink-0"
                {{ $isChecked ? 'checked' : '' }}>

            <span class="category-name text-xs sm:text-sm text-gray-700 group-hover:text-gray-900 leading-tight flex-1">
                {{ $category->name }}
            </span>

            @if (isset($category->products_count))
                <span class="text-xs text-gray-400 ml-2">
                    ({{ $category->products_count }})
                </span>
            @endif
        </label>
    </div>

    @if ($category->children->count() > 0)
        <div class="category-children ml-4 border-l-2 border-gray-200 pl-2 mt-1 {{ $open ? '' : 'hidden' }}">
            @foreach ($category->children as $child)
                @include('components.category-tree-item', [
                    'category' => $child,
                    'level' => ($level ?? 0) + 1,
                    'selectedCategoryId' => $selectedCategoryId ?? null,
                ])
            @endforeach
        </div>
    @endif
</div>
