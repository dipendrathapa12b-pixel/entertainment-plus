
@extends('layouts.app')

@section('title', 'Watch & Discover Anime, Movies and TV Shows | ENTERTAINMENT+')

@section('description', 'Discover trending anime, upcoming anime, currently airing series, top-rated anime, movies and TV shows. Explore anime genres and find your next favourite entertainment.')

@section('content')

@php
    // Get currently airing anime
    $airingList = collect($airingAnime ?? []);

    // Select the most recently started anime
    $featured = $airingList
        ->filter(function ($anime) {
            return !empty(data_get($anime, 'aired.from'));
        })
        ->sortByDesc(function ($anime) {
            return strtotime(data_get($anime, 'aired.from'));
        })
        ->first();

    // Fallback if airing dates are unavailable
    $featured = $featured
        ?? $airingList->first()
        ?? null;

    // Featured anime information
    $featuredTitle = data_get($featured, 'title')
        ?? data_get($featured, 'title_english')
        ?? 'Currently Airing Anime';

    $featuredImage = data_get($featured, 'images.jpg.large_image_url')
        ?? data_get($featured, 'images.webp.large_image_url')
        ?? data_get($featured, 'images.jpg.image_url')
        ?? data_get($featured, 'image')
        ?? data_get($featured, 'image_url');

    $featuredSynopsis = data_get($featured, 'synopsis')
        ?? 'Discover the latest currently airing anime and follow exciting new episodes.';

    $featuredId = data_get($featured, 'mal_id')
        ?? data_get($featured, 'id');

    $featuredRating = data_get($featured, 'score');

    $featuredEpisodes = data_get($featured, 'episodes');

    $featuredStatus = data_get($featured, 'status', 'Currently Airing');
@endphp

<div class="min-h-screen bg-[#09090f]">

    {{-- FEATURED ANIME HERO --}}
    <section class="relative overflow-hidden">

        @if($featuredImage)
            <div class="absolute inset-0">
                <img
                    src="{{ $featuredImage }}"
                    alt=""
                    class="h-full w-full object-cover opacity-20 blur-sm"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#09090f] via-[#09090f]/90 to-[#09090f]/40"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#09090f] via-transparent to-[#09090f]/40"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#302052] via-[#171326] to-[#09090f]"></div>
        @endif

        <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 md:grid-cols-2 md:py-24">

            <div class="max-w-2xl">

                <span class="inline-flex items-center gap-2 rounded-full border border-[#a78bfa]/30 bg-[#a78bfa]/10 px-4 py-2 text-xs font-bold uppercase tracking-widest text-[#c4b5fd]">
                    <span class="h-2 w-2 rounded-full bg-[#a78bfa]"></span>
                    Welcome to AnimeVerse
                </span>

                <h1 class="mt-7 text-4xl font-black leading-tight text-white sm:text-5xl md:text-6xl">
                    Your Next
                    <span class="block text-[#a78bfa]">Anime Adventure</span>
                    Starts Here.
                </h1>

                <p class="mt-6 max-w-xl text-base leading-8 text-gray-300">
                    Discover trending anime, explore legendary series,
                    follow new episodes and find your next favourite story.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">

                    <a href="{{ $featuredId ? route('anime.show', $featuredId) : route('anime.index') }}"
                       class="rounded-xl bg-[#a78bfa] px-7 py-3.5 font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                        Explore Featured Anime
                    </a>

                    <a href="{{ route('movies.index') }}"
                       class="rounded-xl border border-white/20 bg-white/5 px-7 py-3.5 font-bold text-white transition hover:bg-white/10">
                        Browse Movies
                    </a>

                </div>

                <div class="mt-10 flex flex-wrap gap-8 text-sm">
                    <div>
                        <p class="text-2xl font-black text-white">Anime</p>
                        <p class="mt-1 text-gray-500">Main experience</p>
                    </div>

                    <div>
                        <p class="text-2xl font-black text-white">Movies</p>
                        <p class="mt-1 text-gray-500">Extra entertainment</p>
                    </div>

                    <div>
                        <p class="text-2xl font-black text-white">TV Shows</p>
                        <p class="mt-1 text-gray-500">More to discover</p>
                    </div>
                </div>

            </div>

            {{-- FEATURED POSTER --}}
            <div class="hidden justify-center md:flex">
                @if($featuredImage)
                    <a href="{{ $featuredId ? route('anime.show', $featuredId) : route('anime.index') }}"
                       class="group relative block w-64 overflow-hidden rounded-3xl border border-[#a78bfa]/30 shadow-2xl shadow-purple-950/50 lg:w-80">

                        <img
                            src="{{ $featuredImage }}"
                            alt="{{ $featuredTitle }}"
                            class="aspect-[2/3] w-full object-cover transition duration-500 group-hover:scale-105"
                        >

                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/70 to-transparent p-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#c4b5fd]">
                                Featured Anime
                            </p>
                            <h2 class="mt-2 text-xl font-black text-white">
                                {{ $featuredTitle }}
                            </h2>
                        </div>

                    </a>
                @endif
            </div>

        </div>
    </section>


    {{-- SEARCH --}}
    <section class="mx-auto max-w-7xl px-4 py-8">

        <form action="{{ route('search') }}" method="GET"
              class="mx-auto flex max-w-2xl gap-2">

            <input
                type="search"
                name="q"
                placeholder="Search anime, movies or TV shows..."
                required
                class="min-w-0 flex-1 rounded-xl border border-white/10 bg-[#13131e] px-5 py-4 text-white outline-none transition placeholder:text-gray-500 focus:border-[#a78bfa]"
            >

            <button type="submit"
                    class="rounded-xl bg-[#a78bfa] px-6 font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                Search
            </button>

        </form>

    </section>


    {{-- API ERRORS --}}
    @foreach(($apiErrors ?? []) as $error)
        <div class="mx-auto mb-5 max-w-7xl px-4">
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-300">
                {{ $error }}
            </div>
        </div>
    @endforeach


    {{-- ========================================= --}}
    {{-- ANIME SECTION - 70% OF HOMEPAGE --}}
    {{-- ========================================= --}}

    <div class="mx-auto max-w-7xl px-4">

        <div class="mb-8 flex items-end justify-between border-b border-white/10 pb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.25em] text-[#a78bfa]">
                    AnimeVerse Collection
                </span>

                <h2 class="mt-2 text-3xl font-black text-white md:text-4xl">
                    Explore Anime
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    Your destination for anime discovery.
                </p>
            </div>

            <a href="{{ route('anime.index') }}"
               class="hidden text-sm font-bold text-[#c4b5fd] hover:text-white sm:block">
                View All Anime →
            </a>
        </div>

           {{-- UPCOMING ANIME --}}
        @include('partials.media-section', [
            'title' => 'Upcoming Anime',
            'items' => $upcomingAnime ?? [],
            'type' => 'anime',
            'source' => 'anime'
        ])



        {{-- CURRENTLY AIRING --}}
        @include('partials.media-section', [
            'title' => 'Currently Airing',
            'items' => $airingAnime ?? [],
            'type' => 'anime',
            'source' => 'anime'
        ])

           {{-- TRENDING MOVIES --}}
            @include('partials.media-section', [
                'title' => 'Trending Movies',
                'items' => $trendingMovies ?? [],
                'type' => 'movie',
                'source' => 'tmdb'
            ])


        {{-- TRENDING ANIME --}}
        @include('partials.media-section', [
            'title' => 'Trending Anime',
            'items' => $trendingAnime ?? [],
            'type' => 'anime',
            'source' => 'anime'
        ])

          {{-- TOP RATED ANIME --}}
        @include('partials.media-section', [
            'title' => 'Top Rated Anime',
            'items' => $topAnime ?? [],
            'type' => 'anime',
            'source' => 'anime'
        ])

        {{-- POPULAR ANIME --}}
        @include('partials.media-section', [
            'title' => 'Popular Anime',
            'items' => $popularAnime ?? [],
            'type' => 'anime',
            'source' => 'anime'
        ])



        {{-- ANIME DISCOVERY BANNER --}}
        <section class="my-14 rounded-3xl border border-[#a78bfa]/20 bg-gradient-to-r from-[#211936] to-[#11101b] p-7 md:p-10">

            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-center">

                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                        Find Your Next Favourite
                    </span>

                    <h2 class="mt-3 text-2xl font-black text-white md:text-3xl">
                        Explore Anime Genres
                    </h2>

                    <p class="mt-3 max-w-xl text-sm leading-7 text-gray-400">
                        From action and adventure to romance, comedy,
                        fantasy and more. Discover anime that matches your taste.
                    </p>
                </div>

                <a href="{{ route('genres.index') }}"
                   class="inline-flex w-fit rounded-xl bg-[#a78bfa] px-6 py-3 font-bold text-[#09090f] hover:bg-[#c4b5fd]">
                    Browse Genres →
                </a>

            </div>

        </section>

    </div>


    {{-- ========================================= --}}
    {{-- MOVIES AND TV - 30% OF HOMEPAGE --}}
    {{-- ========================================= --}}

    <section class="border-t border-white/10 bg-[#0d0d16]">

        <div class="mx-auto max-w-7xl px-4 py-14">

            <div class="mb-8 flex items-end justify-between">

                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.25em] text-gray-500">
                        More Entertainment
                    </span>

                    <h2 class="mt-2 text-3xl font-black text-white md:text-4xl">
                        Movies & TV Shows
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Discover movies and television series alongside anime.
                    </p>
                </div>

                <a href="{{ route('movies.index') }}"
                   class="hidden text-sm font-bold text-[#a78bfa] hover:text-white sm:block">
                    Explore Movies & TV →
                </a>

            </div>


            {{-- TRENDING MOVIES --}}
            @include('partials.media-section', [
                'title' => 'Trending Movies',
                'items' => $trendingMovies ?? [],
                'type' => 'movie',
                'source' => 'tmdb'
            ])


            {{-- LATEST MOVIES --}}
            @include('partials.media-section', [
                'title' => 'Latest Movies',
                'items' => $latestMovies ?? [],
                'type' => 'movie',
                'source' => 'tmdb'
            ])


            {{-- POPULAR TV --}}
            @include('partials.media-section', [
                'title' => 'Popular TV Shows',
                'items' => $popularTV ?? [],
                'type' => 'tv',
                'source' => 'tmdb'
            ])


            {{-- KOREAN DRAMA --}}
            @include('partials.media-section', [
                'title' => 'Korean Dramas',
                'items' => $koreanDramas ?? [],
                'type' => 'tv',
                'source' => 'tmdb'
            ])

        </div>

    </section>

</div>

@endsection
