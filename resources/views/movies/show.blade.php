@extends('layouts.app')

@php
    $isMovie = $type === 'movie';

    $id = $movie['id'] ?? null;
    $title = $movie['title'] ?? ($movie['name'] ?? 'Unknown Title');

    $originalTitle = $movie['original_title'] ?? ($movie['original_name'] ?? null);

    $poster = !empty($movie['poster_path'])
        ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
        : 'https://placehold.co/500x750/13131e/ffffff?text=No+Poster';

    $backdrop = !empty($movie['backdrop_path'])
        ? 'https://image.tmdb.org/t/p/original' . $movie['backdrop_path']
        : $poster;

    $releaseDate = $movie['release_date'] ?? ($movie['first_air_date'] ?? null);

    $year = $releaseDate ? substr($releaseDate, 0, 4) : 'Unknown';

    $rating = $movie['vote_average'] ?? null;
    $runtime = $movie['runtime'] ?? ($movie['episode_run_time'][0] ?? null);

    $genres = $movie['genres'] ?? [];
    $status = $movie['status'] ?? 'Unknown';
    $overview = $movie['overview'] ?? 'No description is available yet.';

    $companies = $movie['production_companies'] ?? [];
    $networks = $movie['networks'] ?? [];

    $seasons = $movie['number_of_seasons'] ?? null;
    $episodes = $movie['number_of_episodes'] ?? null;

    $languages = $movie['spoken_languages'] ?? [];

    $videos = $videos ?? data_get($movie, 'videos.results', []);

    $trailer =
        collect($videos)->first(function ($video) {
            return ($video['site'] ?? '') === 'YouTube' &&
                ($video['type'] ?? '') === 'Trailer' &&
                ($video['official'] ?? false);
        }) ??
        collect($videos)->first(function ($video) {
            return ($video['site'] ?? '') === 'YouTube' && ($video['type'] ?? '') === 'Trailer';
        });

    $recommendations = $recommendations ?? [];
    $cast = $cast ?? data_get($movie, 'credits.cast', []);
@endphp

@section('title', $title . ' | ENTERTAINMENT+')

@section('description', \Illuminate\Support\Str::limit(strip_tags($overview), 155))

@section('content')

    {{-- DETAILS HERO --}}
    <section class="relative overflow-hidden">

        {{-- BACKDROP --}}
        <div class="absolute inset-0">
            <img src="{{ $backdrop }}" alt="" class="h-full w-full object-cover opacity-25">

            <div class="absolute inset-0 bg-gradient-to-t from-[#09090f] via-[#09090f]/85 to-[#09090f]/30"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 md:py-20 lg:px-8">

            {{-- BACK BUTTON --}}
            <div class="mb-8">
                <a href="{{ route('movies.index') }}" class="text-sm text-gray-400 transition hover:text-[#c4b5fd]">
                    ← Back to Movies & TV
                </a>
            </div>

            <div class="grid grid-cols-1 items-start gap-10 md:grid-cols-3">

                {{-- POSTER --}}
                <div class="md:col-span-1">

                    <img src="{{ $poster }}" alt="{{ $title }}"
                        class="mx-auto w-full max-w-sm rounded-xl border border-white/10 shadow-2xl">

                    {{-- WATCH MOVIE / TV EPISODES --}}
                    @if ($id && $isMovie)
                        <a href="{{ route('movies.watch', ['type' => 'movie', 'id' => $id]) }}"
                            class="mt-6 block w-full rounded-lg bg-[#a78bfa] px-6 py-3 text-center font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                            ▶ Watch Movie
                        </a>
                    @elseif($id)
                        <a href="{{ route('movies.tv.episode', [
                            'id' => $id,
                            'season' => 1,
                            'episode' => 1,
                        ]) }}"
                            class="mt-6 block w-full rounded-lg bg-[#a78bfa] px-6 py-3 text-center font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                            ▶ Watch Now
                        </a>
                    @endif

                    {{-- TRAILER BUTTON --}}
                    @if ($trailer && !empty($trailer['key']))
                        <a href="https://www.youtube.com/watch?v={{ $trailer['key'] }}" target="_blank"
                            rel="noopener noreferrer"
                            class="mt-3 block w-full rounded-lg bg-red-600 px-6 py-3 text-center font-bold text-white transition hover:bg-red-700">
                            ▶ Watch Trailer
                        </a>
                    @endif

                </div>

                {{-- INFORMATION --}}
                <div class="min-w-0 pt-2 md:col-span-2">

                    <p class="mb-3 text-sm font-bold uppercase tracking-widest text-[#a78bfa]">
                        {{ $isMovie ? 'Movie Details' : 'TV Series Details' }}
                    </p>

                    <h1 class="mb-4 break-words text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">
                        {{ $title }}
                    </h1>

                    @if ($originalTitle && $originalTitle !== $title)
                        <p class="mb-4 text-lg text-gray-400">
                            {{ $originalTitle }}
                        </p>
                    @endif

                    @if (!empty($movie['tagline']))
                        <p class="mb-5 text-lg italic text-gray-300">
                            "{{ $movie['tagline'] }}"
                        </p>
                    @endif

                    {{-- RATINGS --}}
                    <div class="mb-6 flex flex-wrap items-center gap-4 text-sm text-gray-300 sm:text-base">

                        @if ($rating !== null)
                            <span class="font-bold text-yellow-400">
                                ★ {{ number_format((float) $rating, 1) }}/10
                            </span>
                        @endif

                        <span>{{ $year }}</span>

                        @if ($isMovie && $runtime)
                            <span>{{ $runtime }} min</span>
                        @endif

                        @if (!$isMovie)
                            <span>{{ $seasons ?? '?' }} Seasons</span>
                            <span>{{ $episodes ?? '?' }} Episodes</span>
                        @endif

                        <span>{{ $status }}</span>

                    </div>



                    {{-- GENRES --}}
                    @if (count($genres))
                        <div class="mb-6 flex flex-wrap gap-2">
                            @foreach ($genres as $genre)
                                <span class="rounded-full border border-[#a78bfa]/40 px-3 py-1 text-sm text-[#c4b5fd]">
                                    {{ $genre['name'] ?? 'Genre' }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- OVERVIEW --}}
                    <h2 class="mb-3 text-2xl font-bold">
                        Overview
                    </h2>

                    <p class="mb-8 whitespace-pre-line text-sm leading-8 text-gray-300 sm:text-base">
                        {{ $overview }}
                    </p>

                    {{-- EXTRA INFORMATION --}}
                    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <p class="text-sm text-gray-500">Release Date</p>
                            <p class="font-semibold">{{ $releaseDate ?: 'Unknown' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Original Language</p>
                            <p class="font-semibold uppercase">
                                {{ $movie['original_language'] ?? 'Unknown' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Popularity</p>
                            <p class="font-semibold">
                                {{ number_format($movie['popularity'] ?? 0, 0) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold">{{ $status }}</p>
                        </div>

                        @if ($isMovie && !empty($movie['budget']))
                            <div>
                                <p class="text-sm text-gray-500">Budget</p>
                                <p class="font-semibold">
                                    ${{ number_format($movie['budget']) }}
                                </p>
                            </div>
                        @endif

                        @if ($isMovie && !empty($movie['revenue']))
                            <div>
                                <p class="text-sm text-gray-500">Revenue</p>
                                <p class="font-semibold">
                                    ${{ number_format($movie['revenue']) }}
                                </p>
                            </div>
                        @endif

                    </div>

                    {{-- PRODUCTION COMPANIES --}}
                    @if (count($companies))
                        <div class="mb-6">
                            <p class="mb-2 text-sm text-gray-500">Production Companies</p>

                            <p class="font-semibold">
                                {{ collect($companies)->pluck('name')->filter()->implode(', ') ?: 'Unknown' }}
                            </p>
                        </div>
                    @endif

                    {{-- TV NETWORKS --}}
                    @if (!$isMovie && count($networks))
                        <div class="mb-6">
                            <p class="mb-2 text-sm text-gray-500">Networks</p>

                            <p class="font-semibold">
                                {{ collect($networks)->pluck('name')->filter()->implode(', ') }}
                            </p>
                        </div>
                    @endif

                    {{-- LANGUAGES --}}
                    @if (count($languages))
                        <div class="mb-6">
                            <p class="mb-2 text-sm text-gray-500">Spoken Languages</p>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($languages as $language)
                                    <span class="rounded-md bg-[#13131e] px-3 py-1 text-sm text-gray-300">
                                        {{ $language['english_name'] ?? ($language['name'] ?? 'Unknown') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    {{-- TRAILER SECTION --}}
    @if ($trailer && !empty($trailer['key']))
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-[#a78bfa]">
                Official Preview
            </p>

            <h2 class="mb-6 text-2xl font-bold">
                Watch Trailer
            </h2>

            <div class="aspect-video max-w-4xl overflow-hidden rounded-xl border border-white/10 bg-[#13131e]">
                <iframe src="https://www.youtube-nocookie.com/embed/{{ $trailer['key'] }}"
                    title="{{ $title }} Trailer" class="h-full w-full" loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>

        </section>
    @endif

    {{-- CAST --}}
    @if (count($cast))
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

            <p class="text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                People Behind The Story
            </p>

            <h2 class="mb-7 mt-1 text-2xl font-bold">
                Cast
            </h2>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">

                @foreach (collect($cast)->take(12) as $person)
                    <div class="overflow-hidden rounded-xl border border-white/10 bg-[#13131e]">

                        @if (!empty($person['profile_path']))
                            <img src="https://image.tmdb.org/t/p/w185{{ $person['profile_path'] }}"
                                alt="{{ $person['name'] ?? 'Cast member' }}" loading="lazy"
                                class="aspect-[2/3] w-full object-cover">
                        @else
                            <div class="flex aspect-[2/3] items-center justify-center bg-[#09090f] text-3xl text-gray-600">
                                ?
                            </div>
                        @endif

                        <div class="p-3">
                            <p class="line-clamp-1 text-sm font-bold">
                                {{ $person['name'] ?? 'Unknown' }}
                            </p>

                            <p class="mt-1 line-clamp-2 text-xs text-gray-400">
                                {{ $person['character'] ?? '' }}
                            </p>
                        </div>

                    </div>
                @endforeach

            </div>
        </section>
    @endif

    {{-- RECOMMENDATIONS --}}
    @if (count($recommendations))
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

            <p class="text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                Keep Exploring
            </p>

            <h2 class="mb-7 mt-1 text-2xl font-bold">
                You May Also Like
            </h2>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">

                @foreach (collect($recommendations)->take(12) as $item)
                    @php
                        $itemId = $item['id'] ?? null;
                        $itemTitle = $item['title'] ?? ($item['name'] ?? 'Untitled');
                        $itemType = $item['media_type'] ?? $type;
                        $itemPoster = $item['poster_path'] ?? null;
                    @endphp

                    @if ($itemId && in_array($itemType, ['movie', 'tv']))
                        <a href="{{ route('movies.show', ['type' => $itemType, 'id' => $itemId]) }}"
                            class="group min-w-0 overflow-hidden rounded-xl border border-white/10 bg-[#13131e] transition hover:-translate-y-1 hover:border-[#a78bfa]/50">

                            @if ($itemPoster)
                                <img src="https://image.tmdb.org/t/p/w342{{ $itemPoster }}" alt="{{ $itemTitle }}"
                                    loading="lazy"
                                    class="aspect-[2/3] w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                            @else
                                <div
                                    class="flex aspect-[2/3] items-center justify-center bg-[#09090f] text-xs text-gray-500">
                                    No Poster
                                </div>
                            @endif

                            <div class="p-3">
                                <p class="line-clamp-2 text-sm font-bold transition group-hover:text-[#c4b5fd]">
                                    {{ $itemTitle }}
                                </p>

                                <div class="mt-2 flex items-center justify-between gap-2 text-xs text-gray-400">
                                    <span>{{ $itemType === 'movie' ? 'Movie' : 'TV Series' }}</span>

                                    @if (isset($item['vote_average']))
                                        <span class="text-yellow-400">
                                            ★ {{ number_format((float) $item['vote_average'], 1) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </a>
                    @endif
                @endforeach

            </div>
        </section>
    @endif

@endsection
