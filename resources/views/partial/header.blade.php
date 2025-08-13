<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="" class="text-2xl font-bold text-blue-600">AuctionsClone</a>
        <form action="" method="get" class="flex items-center">
            <label for="search" class="sr-only">Tìm kiếm</label>
            <input id="search" name="q" type="search"
                placeholder="Tìm kiếm..."
                value="{{ request('q') }}"
                class="px-3 py-2 border rounded-l-md focus:ring-2 focus:ring-blue-500" />
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md">Tìm</button>
        </form>
    </div>
</header>
