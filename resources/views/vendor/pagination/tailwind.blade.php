@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
        class="flex flex-col sm:flex-row items-center justify-between bg-white rounded-xl shadow-lg p-6">

        <div class="mb-4 sm:mb-0">
            <p class="text-sm text-gray-700">
                Hiển thị
                <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
                đến
                <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
                trong tổng số
                <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
            </p>
        </div>

        <div class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-1"></i>
                    <span class="hidden sm:inline">Trước</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                    <i class="fas fa-chevron-left mr-1"></i>
                    <span class="hidden sm:inline">Trước</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="flex items-center space-x-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span
                            class="flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-500">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="flex items-center justify-center w-10 h-10 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                    <span class="hidden sm:inline">Sau</span>
                    <i class="fas fa-chevron-right ml-1"></i>
                </a>
            @else
                <span
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    <span class="hidden sm:inline">Sau</span>
                    <i class="fas fa-chevron-right ml-1"></i>
                </span>
            @endif

            {{-- Jump to Page (Optional) --}}
            <div class="hidden md:flex items-center ml-4 space-x-2">
                <span class="text-sm text-gray-700">Đi đến:</span>
                <input type="number" min="1" max="{{ $paginator->lastPage() }}"
                    value="{{ $paginator->currentPage() }}"
                    class="w-16 px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    onchange="jumpToPage(this.value)">
                <button onclick="jumpToPage(document.querySelector('input[type=number]').value)"
                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Đi
                </button>
            </div>
        </div>
    </nav>

    <script>
        function jumpToPage(page) {
            if (page >= 1 && page <= {{ $paginator->lastPage() }}) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('page', page);
                window.location.href = currentUrl.toString();
            }
        }
    </script>
@endif
