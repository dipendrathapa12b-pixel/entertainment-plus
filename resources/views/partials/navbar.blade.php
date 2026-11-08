
<nav class="sticky top-0 z-50 border-b border-white/10 bg-[#09090f]/95 backdrop-blur-xl">

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4">

        {{-- LOGO --}}
        <a href="{{ route('home') }}"
            class="shrink-0 text-2xl font-black tracking-tight text-white">
            ENTERTAINMENT<span class="text-[#a78bfa]">+</span>
        </a>

        {{-- MOBILE MENU BUTTON --}}
        <button id="mobile-menu-toggle"
            type="button"
            class="rounded-lg border border-white/10 px-3 py-2 text-white md:hidden"
            aria-label="Open navigation menu"
            aria-expanded="false">
            ☰
        </button>

        {{-- NAVIGATION LINKS --}}
        <div id="nav-menu"
            class="absolute left-0 top-full hidden w-full border-b border-white/10 bg-[#09090f] p-5 md:static md:flex md:w-auto md:items-center md:gap-5 md:border-0 md:bg-transparent md:p-0">

            <a href="{{ route('home') }}"
                class="block py-2 font-medium text-white transition hover:text-[#a78bfa]">
                Home
            </a>

            <a href="{{ route('anime.index') }}"
                class="block py-2 font-medium text-[#c4b5fd] transition hover:text-white">
                Anime
            </a>

            <a href="{{ route('anime.category', 'popular') }}"
                class="block py-2 font-medium text-gray-300 transition hover:text-[#a78bfa]">
                Popular
            </a>

            <a href="{{ route('anime.category', 'top') }}"
                class="block py-2 font-medium text-gray-300 transition hover:text-[#a78bfa]">
                Top Rated
            </a>

            <a href="{{ route('genres.index') }}"
                class="block py-2 font-medium text-gray-300 transition hover:text-[#a78bfa]">
                Genres
            </a>

            {{-- LIVE SEARCH --}}
            <form action="{{ route('search') }}"
                method="GET"
                id="navbar-search-form"
                class="relative my-3 w-full md:my-0 md:w-64 lg:w-72">

                <div class="flex overflow-hidden rounded-lg border border-white/10 bg-[#13131e] transition focus-within:border-[#a78bfa]">

                    <input
                        type="search"
                        id="navbar-search-input"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search anime, movies..."
                        autocomplete="off"
                        class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm text-white outline-none placeholder:text-gray-500">

                    <button type="submit"
                        class="shrink-0 bg-[#a78bfa] px-4 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                        Search
                    </button>

                </div>

                {{-- LIVE SUGGESTIONS --}}
                <div id="navbar-search-results"
                    class="absolute left-0 right-0 top-full z-[60] mt-2 hidden max-h-96 overflow-y-auto rounded-xl border border-white/10 bg-[#13131e] shadow-2xl">
                </div>

            </form>

            {{-- MOVIES & TV --}}
            <a href="{{ route('movies.index') }}"
                class="mt-2 block rounded-lg border border-white/10 px-4 py-2 text-sm font-semibold text-gray-300 transition hover:border-[#a78bfa]/50 hover:text-white md:mt-0">
                Movies & TV
            </a>

        </div>

    </div>

</nav>

<script>

document.addEventListener('DOMContentLoaded', function () {

    // MOBILE MENU
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('nav-menu');

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            const isHidden = menu.classList.toggle('hidden');

            toggle.setAttribute('aria-expanded', String(!isHidden));
            toggle.textContent = isHidden ? '☰' : '✕';
        });
    }

    // COMBINED LIVE SEARCH
    const searchInput = document.getElementById('navbar-search-input');
    const searchResults = document.getElementById('navbar-search-results');

    if (!searchInput || !searchResults) return;

    const searchUrl = @json(route('search.suggestions'));
    const animeUrl = @json(route('anime.show', ['id' => 'ANIME_ID']));
    const movieUrl = @json(route('movies.show', [
        'type' => 'MEDIA_TYPE',
        'id' => 'MEDIA_ID'
    ]));

    let debounceTimer;
    let activeController = null;
    let activeIndex = -1;

    function hideResults() {
        searchResults.classList.add('hidden');
        searchResults.replaceChildren();
    }

    function showMessage(message) {
        searchResults.replaceChildren();

        const element = document.createElement('div');
        element.className = 'px-4 py-3 text-sm text-gray-400';
        element.textContent = message;

        searchResults.appendChild(element);
        searchResults.classList.remove('hidden');
    }

    function createHeading(text) {
        const heading = document.createElement('div');
        heading.className = 'border-b border-white/10 px-3 py-2 text-xs font-bold uppercase tracking-wider text-[#a78bfa]';
        heading.textContent = text;
        searchResults.appendChild(heading);
    }

    function createSuggestion(item, type) {
        const isAnime = type === 'anime';

        const id = isAnime
            ? (item.mal_id ?? item.id)
            : item.id;

        if (!id) return;

        const title = isAnime
            ? (item.title ?? item.title_english ?? item.name ?? 'Unknown Anime')
            : (item.title ?? item.name ?? 'Unknown Title');

        const imageUrl = isAnime
            ? (
                item.images?.webp?.large_image_url ??
                item.images?.jpg?.large_image_url ??
                item.image_url ??
                item.poster ??
                null
            )
            : (
                item.poster_path
                    ? 'https://image.tmdb.org/t/p/w92' + item.poster_path
                    : null
            );

        const link = document.createElement('a');

        if (isAnime) {
            link.href = animeUrl.replace(
                'ANIME_ID',
                encodeURIComponent(id)
            );
        } else {
            const mediaType = item.media_type;

            if (!['movie', 'tv'].includes(mediaType)) return;

            link.href = movieUrl
                .replace('MEDIA_TYPE', mediaType)
                .replace('MEDIA_ID', encodeURIComponent(id));
        }

        link.className = 'flex items-center gap-3 border-b border-white/5 px-3 py-3 transition hover:bg-white/5';

        if (imageUrl && /^https?:\/\//i.test(imageUrl)) {
            const image = document.createElement('img');
            image.src = imageUrl;
            image.alt = '';
            image.loading = 'lazy';
            image.className = 'h-14 w-11 shrink-0 rounded-md object-cover';
            link.appendChild(image);
        }

        const info = document.createElement('div');
        info.className = 'min-w-0 flex-1';

        const name = document.createElement('p');
        name.className = 'truncate text-sm font-semibold text-white';
        name.textContent = title;
        info.appendChild(name);

        const details = document.createElement('p');
        details.className = 'mt-1 text-xs text-gray-400';

        const rating = isAnime
            ? (item.score ?? item.rating)
            : item.vote_average;

        const label = isAnime
            ? 'Anime'
            : item.media_type === 'tv'
                ? 'TV Show'
                : 'Movie';

        details.textContent = rating
            ? `★ ${Number(rating).toFixed(1)} · ${label}`
            : label;

        info.appendChild(details);
        link.appendChild(info);

        searchResults.appendChild(link);
    }

    async function search(query) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                signal: activeController.signal
            });

            if (!response.ok) {
                throw new Error('Search request failed');
            }

            const data = await response.json();

            searchResults.replaceChildren();

            const anime = Array.isArray(data.anime)
                ? data.anime
                : [];

            const movies = Array.isArray(data.moviesAndTV)
                ? data.moviesAndTV
                : [];

            if (!anime.length && !movies.length) {
                showMessage('No results found.');
                return;
            }

            if (anime.length) {
                createHeading('Anime');

                anime.slice(0, 5).forEach(item => {
                    createSuggestion(item, 'anime');
                });
            }

            if (movies.length) {
                createHeading('Movies & TV Shows');

                movies.slice(0, 5).forEach(item => {
                    createSuggestion(item, 'movie');
                });
            }

            searchResults.classList.remove('hidden');

        } catch (error) {
            if (error.name !== 'AbortError') {
                showMessage('Unable to load suggestions.');
            }
        }
    }

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        clearTimeout(debounceTimer);

        if (activeController) {
            activeController.abort();
        }

        if (query.length < 2) {
            hideResults();
            return;
        }

        showMessage('Searching...');

        debounceTimer = setTimeout(() => search(query), 350);
    });

    document.addEventListener('click', function (event) {
        if (
            !searchInput.contains(event.target) &&
            !searchResults.contains(event.target)
        ) {
            hideResults();
        }
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            hideResults();
        }
    });

});

</script>

