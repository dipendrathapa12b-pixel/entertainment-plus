
@extends('layouts.app')

@section('title', 'Movies & TV Shows | ENTERTAINMENT+')

@section('description', 'Explore trending movies, popular TV shows, Korean dramas and the latest releases on ENTERTAINMENT+.')

@section('content')

@php
    $trendingMovies = $trendingMovies ?? [];
    $popularMovies = $popularMovies ?? [];
    $trendingTV = $trendingTV ?? [];
    $popularTV = $popularTV ?? [];
    $latestMovies = $latestMovies ?? [];
    $koreanDramas = $koreanDramas ?? [];

    $hero = $trendingMovies[0] ?? null;

    $movieSections = [
        [
            'title' => 'Trending Movies',
            'subtitle' => 'Movies everyone is watching',
            'items' => $trendingMovies,
            'type' => 'movie',
            'slug' => 'trending',
        ],
        [
            'title' => 'Popular Movies',
            'subtitle' => 'Popular picks worth watching',
            'items' => $popularMovies,
            'type' => 'movie',
            'slug' => 'popular',
        ],
        [
            'title' => 'Trending TV Shows',
            'subtitle' => 'Trending series and television',
            'items' => $trendingTV,
            'type' => 'tv',
            'slug' => 'trending',
        ],
        [
            'title' => 'Popular TV Shows',
            'subtitle' => 'Discover popular series',
            'items' => $popularTV,
            'type' => 'tv',
            'slug' => 'popular',
        ],
        [
            'title' => 'Latest Movies',
            'subtitle' => 'Recently released movies',
            'items' => $latestMovies,
            'type' => 'movie',
            'slug' => null,
        ],
        [
            'title' => 'Korean Dramas',
            'subtitle' => 'Explore Korean television dramas',
            'items' => $koreanDramas,
            'type' => 'tv',
            'slug' => 'korean',
        ],
    ];
@endphp

<div class="min-h-screen bg-[#09090f] text-white">

    {{-- HERO SECTION --}}
    @if($hero)
        <section class="relative overflow-hidden">

            @if(!empty($hero['backdrop_path']))
                <div class="absolute inset-0">
                    <img
                        src="https://image.tmdb.org/t/p/original{{ $hero['backdrop_path'] }}"
                        alt=""
                        class="h-full w-full object-cover opacity-25"
                    >
                    <div class="absolute inset-0 bg-gradient-to-r from-[#09090f] via-[#09090f]/80 to-[#09090f]/30"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#09090f] via-transparent to-[#09090f]/40"></div>
                </div>
            @endif

            <div class="relative mx-auto flex min-h-[440px] max-w-7xl items-center px-4 py-16 sm:px-6 lg:px-8">

                <div class="max-w-2xl">

                    <span class="inline-flex items-center gap-2 rounded-full border border-[#a78bfa]/30 bg-[#a78bfa]/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#c4b5fd]">
                        <span class="h-2 w-2 rounded-full bg-[#a78bfa]"></span>
                        Trending Now
                    </span>

                    <h1 class="mt-5 text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">
                        {{ $hero['title'] ?? $hero['name'] ?? 'Discover Movies' }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-300">
                        <span>
                            {{ substr($hero['release_date'] ?? $hero['first_air_date'] ?? '', 0, 4) ?: 'New Release' }}
                        </span>

                        <span class="text-gray-600">•</span>

                        <span class="text-[#c4b5fd]">
                            ★ {{ number_format((float) ($hero['vote_average'] ?? 0), 1) }}/10
                        </span>

                        <span class="text-gray-600">•</span>

                        <span>Movie</span>
                    </div>

                    <p class="mt-5 line-clamp-3 max-w-xl text-sm leading-7 text-gray-300 sm:text-base">
                        {{ $hero['overview'] ?? 'Discover exciting movies, trending releases and popular entertainment.' }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">

                        <a
                            href="{{ route('movies.show', ['type' => 'movie', 'id' => $hero['id']]) }}"
                            class="rounded-xl bg-[#a78bfa] px-6 py-3 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]"
                        >
                            Explore Movie
                        </a>

                        <a
                            href="{{ route('movies.category', ['type' => 'movie', 'slug' => 'trending']) }}"
                            class="rounded-xl border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            Browse Trending
                        </a>

                    </div>
                </div>

            </div>
        </section>
    @endif


    {{-- MOVIE CATEGORY NAVIGATION --}}
    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">

        <div class="flex gap-3 overflow-x-auto pb-3">

            @foreach([
                ['name' => 'All Movies', 'url' => route('movies.index')],
                ['name' => 'Trending', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'trending'])],
                ['name' => 'Popular', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'popular'])],
                ['name' => 'Action', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'action'])],
                ['name' => 'Comedy', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'comedy'])],
                ['name' => 'Bollywood', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'bollywood'])],
                ['name' => 'Hollywood', 'url' => route('movies.category', ['type' => 'movie', 'slug' => 'hollywood'])],
                ['name' => 'Korean Drama', 'url' => route('movies.category', ['type' => 'tv', 'slug' => 'korean'])],
            ] as $category)

                <a
                    href="{{ $category['url'] }}"
                    class="shrink-0 rounded-full border border-white/10 bg-[#13131e] px-5 py-2 text-sm font-medium text-gray-300 transition hover:border-[#a78bfa]/50 hover:bg-[#a78bfa]/10 hover:text-[#c4b5fd]"
                >
                    {{ $category['name'] }}
                </a>

            @endforeach

        </div>
    </section>


    {{-- API ERRORS --}}
    @if(!empty($apiErrors))
        <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-yellow-500/20 bg-yellow-500/5 p-4 text-sm text-yellow-300">
                Some movie sections could not be loaded. Please try again later.
            </div>
        </div>
    @endif


    {{-- MOVIE AND TV SECTIONS --}}
    <div class="mx-auto max-w-7xl space-y-12 px-4 py-10 sm:px-6 lg:px-8">

        @foreach($movieSections as $section)

            @if(!empty($section['items']))

                <section>

                    {{-- SECTION HEADER --}}
                    <div class="mb-5 flex items-end justify-between gap-3">

                        <div>
                            <h2 class="text-xl font-bold text-white sm:text-2xl">
                                {{ $section['title'] }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $section['subtitle'] }}
                            </p>
                        </div>

                        @if($section['slug'])
                            <a
                                href="{{ route('movies.category', [
                                    'type' => $section['type'],
                                    'slug' => $section['slug']
                                ]) }}"
                                class="shrink-0 text-sm font-semibold text-[#a78bfa] transition hover:text-[#c4b5fd]"
                            >
                                View All →
                            </a>
                        @endif

                    </div>



{{-- MOVIE GRID --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7">

    @foreach(array_slice($section['items'], 0, 14) as $item)

        @php
            $itemId = $item['id'] ?? null;
            $itemTitle = $item['title'] ?? $item['name'] ?? 'Untitled';
            $itemDate = $item['release_date'] ?? $item['first_air_date'] ?? null;
            $itemYear = $itemDate ? substr($itemDate, 0, 4) : null;
            $poster = $item['poster_path'] ?? null;
            $rating = $item['vote_average'] ?? null;
            $mediaType = $section['type'];
        @endphp

        <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:shadow-xl hover:shadow-purple-950/30">

            {{-- POSTER --}}
            <a
                href="{{ $itemId ? route('movies.show', ['type' => $mediaType, 'id' => $itemId]) : '#' }}"
                class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]"
            >

                @if($poster)

                    <img
                        src="https://image.tmdb.org/t/p/w342{{ $poster }}"
                        alt="{{ $itemTitle }}"
                        loading="lazy"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                    >

                @else

                    <div class="flex h-full items-center justify-center p-3 text-center text-sm text-gray-500">
                        No Poster Available
                    </div>

                @endif

                {{-- DARK GRADIENT --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/10 opacity-70 transition group-hover:opacity-100"></div>

                {{-- RATING --}}
                @if($rating !== null)
                    <div class="absolute right-2 top-2 rounded-md border border-white/10 bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400 backdrop-blur">
                        ★ {{ number_format((float) $rating, 1) }}
                    </div>
                @endif

                {{-- MEDIA TYPE --}}
                <span class="absolute bottom-2 left-2 rounded-md bg-[#a78bfa] px-2 py-1 text-[10px] font-black uppercase tracking-wide text-[#09090f]">
                    {{ $mediaType === 'movie' ? 'Movie' : 'TV Series' }}
                </span>

                {{-- HOVER PLAY ICON --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-0 transition duration-300 group-hover:opacity-100">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full border border-white/30 bg-[#a78bfa]/90 text-[#09090f] shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-6 w-6">
                            <path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11-6.86a1 1 0 0 0 0-1.72l-11-6.86A1 1 0 0 0 8 5.14Z"/>
                        </svg>
                    </div>
                </div>

            </a>

            {{-- DETAILS --}}
            <div class="p-3">

                <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-white transition group-hover:text-[#c4b5fd]">
                    {{ $itemTitle }}
                </h3>

                <div class="mt-2 flex items-center justify-between gap-2 text-xs text-gray-400">

                    <span>{{ $itemYear ?? 'Release unknown' }}</span>

                    @if($rating !== null)
                        <span class="text-yellow-400">
                            ★ {{ number_format((float) $rating, 1) }}
                        </span>
                    @endif

                </div>

                <a
                    href="{{ $itemId ? route('movies.show', ['type' => $mediaType, 'id' => $itemId]) : '#' }}"
                    class="mt-3 block rounded-lg bg-[#29213b] py-2 text-center text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]"
                >
                    View Details
                </a>

            </div>

        </div>

    @endforeach

</div>


                </section>

            @endif

        @endforeach

    </div>

</div>

@endsection
