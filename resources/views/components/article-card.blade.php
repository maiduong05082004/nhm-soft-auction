<article
    class="group bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-100"
    aria-labelledby="article-{{ $article->id }}-title"
>
    <div class="relative">
        @if ($article->image)
            <a href="{{ route('news.detail', $article->slug) }}" class="block w-full h-48 overflow-hidden">
                <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($article->image) }}"
                    alt="{{ $article->title }}"
                    class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-105"
                    loading="lazy"
                    onerror="this.src='{{ asset('images/product_default.jpg') }}'">
            </a>
        @else
            <a href="{{ route('news.detail', $article->slug) }}" class="block w-full h-48 overflow-hidden">
                <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center transform transition-transform duration-500 group-hover:scale-105">
                    <i class="fas fa-newspaper text-white text-3xl"></i>
                </div>
            </a>
        @endif

        <div class="absolute top-4 left-4 transform transition-transform duration-300 group-hover:-translate-y-1 z-30">
            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                {{ $article->category->name }}
            </span>
        </div>
    </div>

    <div class="p-6">
        <h4 id="article-{{ $article->id }}-title"
            class="font-bold text-lg text-gray-900 mb-3  min-h-[3rem] transition-colors duration-300 group-hover:text-blue-600">
            <a href="{{ route('news.detail', $article->slug) }}" class="inline-block line-clamp-2 leading-snug">
                {{ \Illuminate\Support\Str::limit(strip_tags($article->title), 70)  }}
            </a>
        </h4>

        <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-snug min-h-[38px] transition-opacity duration-300 group-hover:opacity-90">
            {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 200) }}
        </p>

        <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-gray-500 flex gap-2 items-center">
                @if (!empty($article['author']['avatar']))
                    <img src="{{ asset('storage/avatar') . '/' . $article['author']['avatar'] }}"
                        class="rounded-full w-6 h-6 object-cover" alt="">
                @else
                    <x-heroicon-o-user class="w-5 h-5 text-gray-400"></x-heroicon-o-user>
                @endif
                <span class="whitespace-nowrap">{{ $article['author']['name'] }}</span>
            </div>

            <div class="text-sm text-gray-500 flex gap-1 items-center">
                <x-heroicon-o-eye class="w-5 h-5"></x-heroicon-o-eye>
                <span class="whitespace-nowrap">{{ $article['view'] }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <span class="text-xs text-gray-500">
                {{ \Carbon\Carbon::parse($article->publish_time)->diffForHumans() }}
            </span>

            <a href="{{ route('news.detail', $article->slug) }}"
                class="text-blue-600 hover:text-blue-800 font-medium text-sm transform transition-colors duration-300 ">
                Đọc thêm <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
    </div>
</article>
