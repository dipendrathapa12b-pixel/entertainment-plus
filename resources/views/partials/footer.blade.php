<footer class="border-t border-white/10 bg-[#09090f]">
    <div class="mx-auto max-w-7xl px-4 py-10">

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">

            {{-- BRAND --}}
            <div>
                <a href="{{ route('home') }}"
                   class="text-2xl font-black tracking-tight text-white">
                    ENTERTAINMENT<span class="text-[#a78bfa]">+</span>
                </a>

                <p class="mt-3 max-w-sm text-sm leading-6 text-gray-400">
                    Your destination for anime, movies and TV shows.
                    Discover your next favourite story with Entertainment+.
                </p>
            </div>

            {{-- ANIME --}}
            <div>
                <h3 class="mb-4 font-bold text-white">Anime</h3>

                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('anime.index') }}" class="hover:text-[#a78bfa]">Browse Anime</a></li>
                    <li><a href="{{ route('genres.index') }}" class="hover:text-[#a78bfa]">Anime Genres</a></li>
                    <li><a href="{{ route('anime.category', 'popular') }}" class="hover:text-[#a78bfa]">Popular Anime</a></li>
                </ul>
            </div>

            {{-- MOVIES --}}
            <div>
                <h3 class="mb-4 font-bold text-white">Movies & TV</h3>

                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('movies.index') }}" class="hover:text-[#a78bfa]">Browse Movies</a></li>
                    <li><a href="{{ route('search') }}" class="hover:text-[#a78bfa]">Search Entertainment</a></li>
                </ul>
            </div>

        </div>

        <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} ENTERTAINMENT+. All rights reserved.
        </div>

    </div>
</footer>
