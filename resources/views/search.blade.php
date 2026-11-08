
@extends('layouts.app')

@section('title', 'Search: ' . ($query ?: 'Entertainment') . ' | ENTERTAINMENT+')

@section('description', 'Search anime, movies and TV shows on ENTERTAINMENT+.')

@section('content')

@php
    $animeResults = collect($anime ?? []);
    $movieResults = collect($moviesAndTV ?? []);

    $getAnimeId = fn ($item) =>
        data_get($item, 'mal_id') ?? data_get($item, 'id');

    $getAnimeTitle = fn ($item) =>
        data_get($item, 'title')
        ?? data_get($item, 'title_english')
        ?? data_get($item, 'name')
        ?? 'Unknown Anime';

    $getAnimeImage = fn ($item) =>
        data_get($item, 'images.webp.large_image_url')
        ?? data_get($item, 'images.jpg.large_image_url')
        ?? data_get($item, 'images.jpg.image_url')
        ?? data_get($item, 'image_url')
        ?? data_get($item, 'poster');

    $getAnimeScore = fn ($item) =>
        data_get($item, 'score') ?? data_get($item, 'rating');

    $getMovieTitle = fn ($item) =>
        data_get($item, 'title') ?? data_get($item, 'name') ?? 'Untitled';

    $getMovieImage = fn ($item) =>
        !empty($item['poster_path'])
            ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path']
            : null;

    $getMovieType = fn ($item) =>
        $item['media_type'] ?? 'movie';

    $totalResults = $animeResults->count() + $movieResults->count();
@endphp

<div class="min-h-screen bg-[#09090f]">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Search heading --}}
        <div class="mb-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#a78bfa]">
                ENTERTAINMENT+ / DISCOVER
            </p>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                Search
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Find your next anime, movie or TV series.
            </p>
        </div>

        {{-- Search form --}}
        <form action="{{ route('search') }}" method="GET"
              class="mb-8 flex flex-col gap-3 sm:flex-row">

            <div class="relative min-w-0 flex-1">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-lg text-gray-500">
                    ⌕
                </span>

                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Search anime, movies and TV shows..."
                    minlength="2"
                    maxlength="100"
                    required
                    class="w-full rounded-xl border border-white/10 bg-[#13131e] py-3.5 pl-12 pr-4 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-[#a78bfa] focus:ring-2 focus:ring-[#a78bfa]/20"
                >
            </div>

            <button type="submit"
                    class="rounded-xl bg-[#a78bfa] px-8 py-3.5 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                Search
            </button>

        </form>

        {{-- Search results --}}
        @if ($query !== '')

            <div class="mb-8 flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-5">
                <div>
                    <h2 class="text-xl font-bold text-white">
                        Results for
                        <span class="text-[#c4b5fd]">"{{ $query }}"</span>
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        {{ $totalResults }} results found
                    </p>
                </div>

                <a href="{{ route('home') }}"
                   class="text-sm font-medium text-gray-400 transition hover:text-[#c4b5fd]">
                    &larr; Home
                </a>
            </div>

            {{-- API errors --}}
            @if (!empty($apiErrors))
                <div class="mb-6 space-y-2">
                    @foreach ($apiErrors as $error)
                        <div class="rounded-lg border border-yellow-500/20 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-300">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ==========================================
                 ANIME RESULTS - MAIN SECTION
            =========================================== --}}
            <section class="mb-14">

                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-6 w-1 rounded-full bg-[#a78bfa]"></span>

                            <h2 class="text-xl font-black text-white sm:text-2xl">
                                Anime Results
                            </h2>
                        </div>

                        <p class="ml-3 mt-1 text-xs text-gray-500">
                            Discover anime matching your search.
                        </p>
                    </div>

                    <span class="rounded-lg bg-[#29213b] px-3 py-1.5 text-xs font-bold text-[#c4b5fd]">
                        {{ $animeResults->count() }} Anime
                    </span>
                </div>

                @if ($animeResults->isNotEmpty())

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7">

                        @foreach ($animeResults as $item)

                            @php
                                $id = $getAnimeId($item);
                                $title = $getAnimeTitle($item);
                                $image = $getAnimeImage($item);
                                $score = $getAnimeScore($item);
                                $year = data_get($item, 'year')
                                    ?? data_get($item, 'aired.prop.from.year');
                            @endphp

                            <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:shadow-lg hover:shadow-purple-950/20">

                                <a href="{{ $id ? route('anime.show', ['id' => $id]) : '#' }}"
                                   class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]">

                                    @if ($image)
                                        <img src="{{ $image }}"
                                             alt="{{ $title }}"
                                             loading="lazy"
                                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-gray-500">
                                            No Poster
                                        </div>
                                    @endif

                                    @if ($score !== null && $score !== '')
                                        <span class="absolute right-2 top-2 rounded-md bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400">
                                            ★ {{ number_format((float) $score, 1) }}
                                        </span>
                                    @endif

                                    <span class="absolute bottom-2 left-2 rounded bg-[#a78bfa] px-2 py-1 text-[10px] font-black uppercase text-[#09090f]">
                                        Anime
                                    </span>
                                </a>

                                <div class="p-3">
                                    <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-white transition group-hover:text-[#c4b5fd]">
                                        {{ $title }}
                                    </h3>

                                    <p class="mt-2 text-xs text-gray-400">
                                        {{ $year ?: 'Anime' }}
                                    </p>

                                    <a href="{{ $id ? route('anime.show', ['id' => $id]) : '#' }}"
                                       class="mt-3 block rounded-lg bg-[#29213b] py-2 text-center text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                                        View Details
                                    </a>
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-white/5 bg-[#13131e] px-5 py-10 text-center">
                        <div class="mb-3 text-3xl">🎌</div>

                        <h3 class="font-bold text-white">
                            No Anime Found
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            No matching anime was found for "{{ $query }}".
                        </p>
                    </div>

                @endif

            </section>

            {{-- ==========================================
                 MOVIES AND TV - SECONDARY SECTION
            =========================================== --}}
            <section class="mb-10">

                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-6 w-1 rounded-full bg-gray-500"></span>

                            <h2 class="text-xl font-black text-white sm:text-2xl">
                                Movies & TV Shows
                            </h2>
                        </div>

                        <p class="ml-3 mt-1 text-xs text-gray-500">
                            More entertainment matching your search.
                        </p>
                    </div>

                    <span class="rounded-lg bg-[#1c1a29] px-3 py-1.5 text-xs font-bold text-gray-300">
                        {{ $movieResults->count() }} Results
                    </span>
                </div>

                @if ($movieResults->isNotEmpty())

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">

                        @foreach ($movieResults as $item)

                            @php
                                $type = $getMovieType($item);
                                $id = $item['id'] ?? null;
                                $title = $getMovieTitle($item);
                                $image = $getMovieImage($item);
                                $rating = $item['vote_average'] ?? null;
                                $year = !empty($item['release_date'])
                                    ? substr($item['release_date'], 0, 4)
                                    : (!empty($item['first_air_date'])
                                        ? substr($item['first_air_date'], 0, 4)
                                        : null);
                            @endphp

                            <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-white/20">

                                <a href="{{ $id ? route('movies.show', ['type' => $type, 'id' => $id]) : '#' }}"
                                   class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]">

                                    @if ($image)
                                        <img src="{{ $image }}"
                                             alt="{{ $title }}"
                                             loading="lazy"
                                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-gray-500">
                                            No Poster
                                        </div>
                                    @endif

                                    @if ($rating !== null)
                                        <span class="absolute right-2 top-2 rounded-md bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400">
                                            ★ {{ number_format((float) $rating, 1) }}
                                        </span>
                                    @endif

                                    <span class="absolute bottom-2 left-2 rounded bg-gray-700 px-2 py-1 text-[10px] font-black uppercase text-white">
                                        {{ $type === 'tv' ? 'TV' : 'Movie' }}
                                    </span>

                                </a>

                                <div class="p-3">

                                    <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-white transition group-hover:text-gray-300">
                                        {{ $title }}
                                    </h3>

                                    <p class="mt-2 text-xs text-gray-400">
                                        {{ $year ?: 'Movie & TV' }}
                                    </p>

                                    <a href="{{ $id ? route('movies.show', ['type' => $type, 'id' => $id]) : '#' }}"
                                       class="mt-3 block rounded-lg bg-[#292929] py-2 text-center text-xs font-bold text-gray-200 transition hover:bg-gray-600">
                                        View Details
                                    </a>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-white/5 bg-[#13131e] px-5 py-8 text-center">
                        <h3 class="font-bold text-white">
                            No Movies or TV Shows Found
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            Try another search term.
                        </p>
                    </div>

                @endif

            </section>

            {{-- No results --}}
            @if ($totalResults === 0 && empty($apiErrors))
                <div class="py-8 text-center">
                    <p class="text-sm text-gray-500">
                        Try searching with a different title.
                    </p>
                </div>
            @endif

        @else

            {{-- Empty search state --}}
            <div class="rounded-2xl border border-white/5 bg-[#13131e] px-5 py-16 text-center">

                <div class="mb-4 text-5xl">🔎</div>

                <h2 class="text-xl font-black text-white">
                    What are you looking for?
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-400">
                    Search thousands of anime, movies and TV shows.
                    Your next favorite title might be just one search away.
                </p>

                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('anime.category', 'popular') }}"
                       class="rounded-lg bg-[#29213b] px-4 py-2 text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        Popular Anime
                    </a>

                    <a href="{{ route('genres.index') }}"
                       class="rounded-lg bg-[#29213b] px-4 py-2 text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        Browse Genres
                    </a>

                    <a href="{{ route('movies.index') }}"
                       class="rounded-lg bg-[#1c1a29] px-4 py-2 text-xs font-bold text-gray-300 transition hover:bg-gray-700">
                        Movies & TV
                    </a>
                </div>

            </div>

        @endif

    </div>
</div>

@endsection
