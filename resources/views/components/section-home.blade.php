<div class="bg-white  shadow-sm  p-6 mb-6">
    <header class="mb-6">
        <h1 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
            {{ $section['title'] . $products->count()}}
        </h1>
    </header>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach ($products->take(10) as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    @if ($products->count() > 10)
        <div class="w-full flex justify-center">
            <div class="my-4">
                <a href="{{ $section['target'] }}"
                    class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                    Xem thêm
                </a>
            </div>
        </div>
    @endif
</div>
