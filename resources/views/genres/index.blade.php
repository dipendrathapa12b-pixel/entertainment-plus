
@extends('layouts.app')

@section('title', 'Anime Genres | ENTERTAINMENT+')

@section('description', 'Explore anime by genre, including action, romance, fantasy, comedy and more.')

@section('content')

@php
    $genreIcons = [
        'action' => '⚔️',
        'adventure' => '🗺️',
        'comedy' => '😂',
        'drama' => '🎭',
        'fantasy' => '🧙',
        'horror' => '👻',
        'mystery' => '🔍',
        'romance' => '❤️',
        'sci-fi' => '🚀',
        'science fiction' => '🚀',
        'slice of life' => '🌸',
        'sports' => '⚽',
        'supernatural' => '✨',
        'thriller' => '🔪',
        'music' => '🎵',
        'psychological' => '🧠',
        'suspense' => '😱',
        'boys love' => '💜',
        'girls love' => '🌷',
        'ecchi' => '🔥',
        'hentai' => '🔞',
    ];
@endphp

<div class="min-h-screen bg-[#09090f]">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Back link --}}
        <a href="{{ route('anime.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-[#a78bfa] transition hover:text-[#c4b5fd]">
            <span>&larr;</span>
            Back to Anime
        </a>

        {{-- Heading --}}
        <div class="mb-9 mt-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#a78bfa]">
                ENTERTAINMENT+ / EXPLORE
            </p>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                Anime Genres
            </h1>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-400 sm:text-base">
                Find your next favorite anime. Explore different genres
                and discover stories that match your interests.
            </p>
        </div>

        {{-- Quick links --}}
        <div class="mb-8 flex flex-wrap gap-2 border-b border-white/10 pb-6">

            <a href="{{ route('anime.index') }}"
               class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white">
                All Anime
            </a>

            <a href="{{ route('anime.category', 'popular') }}"
               class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white">
                Popular
            </a>

            <a href="{{ route('anime.category', 'top') }}"
               class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white">
                Top Rated
            </a>

            <span class="rounded-lg bg-[#a78bfa] px-4 py-2 text-sm font-bold text-[#09090f]">
                Genres
            </span>

        </div>

        {{-- Genre count --}}
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-white">
                Browse by Genre
            </h2>

            <span class="text-xs text-gray-500">
                {{ count($genres) }} Genres
            </span>
        </div>

        {{-- Genre cards --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">

            @forelse ($genres as $genre)

                @php
                    $genreId = data_get($genre, 'mal_id')
                        ?? data_get($genre, 'id')
                        ?? data_get($genre, 'genre_id');

                    $genreName = data_get($genre, 'name')
                        ?? data_get($genre, 'title')
                        ?? 'Unknown Genre';

                    $animeCount = data_get($genre, 'count')
                        ?? data_get($genre, 'anime_count')
                        ?? data_get($genre, 'total_anime');

                    $icon = $genreIcons[strtolower($genreName)] ?? '🎌';
                @endphp

                <a href="{{ $genreId ? route('genres.show', ['id' => $genreId]) : '#' }}"
                   class="group flex min-h-32 flex-col justify-between overflow-hidden rounded-xl border border-white/5 bg-[#13131e] p-4 transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:bg-[#1c1929]">

                    <div class="flex items-start justify-between gap-2">

                        <span class="text-3xl transition duration-300 group-hover:scale-110">
                            {{ $icon }}
                        </span>

                        <span class="text-xl text-[#a78bfa] transition group-hover:translate-x-1">
                            &rarr;
                        </span>

                    </div>

                    <div class="mt-5 min-w-0">

                        <h3 class="truncate text-sm font-bold text-white transition group-hover:text-[#c4b5fd] sm:text-base">
                            {{ $genreName }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            @if ($animeCount !== null)
                                {{ number_format((int) $animeCount) }} Anime
                            @else
                                Explore Anime
                            @endif
                        </p>

                    </div>

                </a>

            @empty

                <div class="col-span-full rounded-xl border border-white/10 bg-[#13131e] px-5 py-16 text-center">

                    <div class="mb-3 text-4xl">🎌</div>

                    <h2 class="text-lg font-bold text-white">
                        Genres Unavailable
                    </h2>

                    <p class="mt-2 text-sm text-gray-400">
                        We couldn't load anime genres right now. Please try again later.
                    </p>

                    <a href="{{ route('anime.index') }}"
                       class="mt-5 inline-block rounded-lg bg-[#a78bfa] px-5 py-2 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                        Browse Anime
                    </a>

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection
