{{-- resources/views/components/category-tree-item.blade.php --}}
<div class="category-item" data-category-id="{{ $category->id }}">
    <div class="flex items-center py-1 px-2 hover:bg-white rounded-md group transition-colors">
        {{-- Toggle button for parent categories --}}
        @if($category->children->count() > 0)
            <button type="button" class="category-toggle mr-1 p-1 hover:bg-gray-200 rounded transition-colors">
                <x-heroicon-o-chevron-right class="h-3 w-3 text-gray-500 transition-transform" />
            </button>
        @else
            <div class="w-5"></div>
        @endif

        {{-- Category checkbox and label --}}
        <label class="flex items-center flex-1 cursor-pointer py-1">
            <input type="checkbox" 
                   value="{{ $category->id }}" 
                   data-full-path="{{ $category->full_path }}"
                   class="checkbox checkbox-xs checkbox-primary mr-2 flex-shrink-0"
                   {{ request('category_id') == $category->id ? 'checked' : '' }}>
            
            <span class="category-name text-xs sm:text-sm text-gray-700 group-hover:text-gray-900 leading-tight flex-1">
                {{ $category->name }}
            </span>
            
            {{-- Product count (optional) --}}
            @if(isset($category->products_count))
                <span class="text-xs text-gray-400 ml-2">
                    ({{ $category->products_count }})
                </span>
            @endif
        </label>
    </div>

    {{-- Children categories --}}
    @if($category->children->count() > 0)
        <div class="category-children ml-4 border-l-2 border-gray-200 pl-2 mt-1 {{ request('category_id') && in_array(request('category_id'), $category->children->pluck('id')->toArray()) ? '' : 'hidden' }}">
            @foreach($category->children as $child)
                @include('components.category-tree-item', [
                    'category' => $child, 
                    'level' => $level + 1,
                    'selectedCategoryId' => $selectedCategoryId
                ])
            @endforeach
        </div>
    @endif
</div>