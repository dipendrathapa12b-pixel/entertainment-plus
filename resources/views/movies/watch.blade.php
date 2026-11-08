
@extends('layouts.app')

@php
    $isMovie = $type === 'movie';
    $id = $movie['id'];

    $title = $movie['title'] ?? $movie['name'] ?? 'Unknown Title';
    $overview = $movie['overview'] ?? 'No description available.';
    $poster = $movie['poster_path'] ?? null;
    $backdrop = $movie['backdrop_path'] ?? null;

    $year = substr(
        $movie['release_date'] ?? $movie['first_air_date'] ?? '',
        0,
        4
    );

    // Stellar movie embed URL
    $stellarUrl = "https://stellar.rip/en/watch/embed/movie/{$id}"
        . "?theme=a78bfa&title=true&poster=true&autoPlay=false";
@endphp

@section('title', 'Watch ' . $title . ' | ENTERTAINMENT+')

@section('content')

<div class="min-h-screen bg-[#09090f] text-white">

    {{-- BACKDROP --}}
    <div class="relative">
        @if($backdrop)
            <div class="absolute inset-0 h-[420px] bg-cover bg-center opacity-20"
                 style="background-image: url('https://image.tmdb.org/t/p/w1280{{ $backdrop }}')">
            </div>
        @endif

        <div class="absolute inset-0 h-[420px] bg-gradient-to-b from-[#09090f]/30 via-[#09090f]/80 to-[#09090f]"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-8">

            {{-- BACK --}}
            <a href="{{ route('movies.show', ['type' => $type, 'id' => $id]) }}"
               class="mb-6 inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#a78bfa]">
                ← Back to Details
            </a>

            {{-- TITLE --}}
            <div class="mb-6">
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                    Now Watching
                </p>

                <h1 class="text-2xl font-black sm:text-3xl">
                    {{ $title }}
                </h1>

                <p class="mt-2 text-sm text-gray-400">
                    {{ $isMovie ? 'Movie' : 'TV Series' }}
                    @if($year)
                        • {{ $year }}
                    @endif
                    • ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}
                </p>
            </div>

            {{-- STELLAR PLAYER --}}
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-black shadow-2xl">

                @if($isMovie)

                    <div class="relative aspect-video w-full">
                        <iframe
                            src="{{ $stellarUrl }}"
                            title="Watch {{ $title }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>
                    </div>

                @else

                    <div class="flex aspect-video flex-col items-center justify-center px-5 text-center">
                        <div class="mb-4 text-5xl text-[#a78bfa]">▶</div>

                        <h2 class="text-xl font-bold">
                            Select a TV Episode
                        </h2>

                        <p class="mt-2 max-w-md text-sm text-gray-400">
                            Choose a season and episode to start watching.
                        </p>

                        <a href="{{ route('movies.tv.episodes', $id) }}"
                           class="mt-6 rounded-lg bg-[#a78bfa] px-6 py-3 text-sm font-bold text-[#09090f] hover:bg-[#c4b5fd]">
                            Browse Seasons & Episodes
                        </a>
                    </div>

                @endif

            </div>

            {{-- MOVIE INFORMATION --}}
            <div class="mt-8 grid gap-6 md:grid-cols-[160px_1fr]">

                @if($poster)
                    <img
                        src="https://image.tmdb.org/t/p/w500{{ $poster }}"
                        alt="{{ $title }}"
                        class="mx-auto w-36 rounded-xl border border-white/10 md:mx-0">
                @endif

                <div>
                    <h2 class="text-xl font-bold">{{ $title }}</h2>

                    <p class="mt-3 text-sm leading-7 text-gray-400">
                        {{ $overview }}
                    </p>

                    <a href="{{ route('movies.show', ['type' => $type, 'id' => $id]) }}"
                       class="mt-5 inline-block rounded-lg border border-white/15 px-5 py-2.5 text-sm font-semibold hover:border-[#a78bfa] hover:text-[#c4b5fd]">
                        View Details
                    </a>
                </div>

            </div>


{{-- SIMILAR MOVIES & TV SHOWS --}}
@php
    $similarTitles = data_get($movie, 'similar.results', []);
@endphp

@if(count($similarTitles))
    <section class="mt-14 border-t border-white/10 pt-10">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                    Discover More
                </p>

                <h2 class="mt-2 text-2xl font-black">
                    Similar {{ $type === 'movie' ? 'Movies' : 'TV Shows' }}
                </h2>
            </div>

            <span class="text-sm text-gray-500">
                {{ count($similarTitles) }} Titles
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7">

            @foreach(array_slice($similarTitles, 0, 14) as $item)

                @php
                    $itemId = $item['id'] ?? null;
                    $itemTitle = $item['title'] ?? $item['name'] ?? 'Unknown Title';
                    $itemPoster = $item['poster_path'] ?? null;
                    $itemType = $item['media_type'] ?? $type;
                    $itemDate = $item['release_date'] ?? $item['first_air_date'] ?? '';
                @endphp

                @if($itemId)
                    <a href="{{ route('movies.show', [
                        'type' => $itemType,
                        'id' => $itemId
                    ]) }}"
                       class="group overflow-hidden rounded-xl border border-white/10 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/60 hover:shadow-lg hover:shadow-[#a78bfa]/10">

                        <div class="relative aspect-[2/3] overflow-hidden bg-[#1b1b29]">

                            @if($itemPoster)
                                <img
                                    src="https://image.tmdb.org/t/p/w500{{ $itemPoster }}"
                                    alt="{{ $itemTitle }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="flex h-full items-center justify-center p-3 text-center text-sm text-gray-500">
                                    No Poster
                                </div>
                            @endif

                            <div class="absolute right-2 top-2 rounded-md bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400">
                                ★ {{ number_format($item['vote_average'] ?? 0, 1) }}
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition group-hover:opacity-100">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#a78bfa] text-lg text-[#09090f]">
                                    ▶
                                </span>
                            </div>

                        </div>

                        <div class="p-3">
                            <h3 class="line-clamp-2 text-sm font-bold text-white transition group-hover:text-[#c4b5fd]">
                                {{ $itemTitle }}
                            </h3>

                            <p class="mt-2 text-xs text-gray-500">
                                {{ $itemDate ? substr($itemDate, 0, 4) : 'Release date unknown' }}
                            </p>
                        </div>

                    </a>
                @endif

            @endforeach

        </div>
    </section>
@endif

        </div>
    </div>
</div>

@endsection
