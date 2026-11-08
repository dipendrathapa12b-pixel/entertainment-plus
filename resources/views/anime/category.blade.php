
@extends('layouts.app')

@section('title', ($title ?? 'Anime Collection') . ' | ENTERTAINMENT+')

@section('description', 'Explore anime titles, ratings and popular anime collections.')

@section('content')

@php
    $currentPage = (int) ($page ?? request()->query('page', 1));
    $currentPage = max(1, $currentPage);

    $lastPage = max(1, (int) ($totalPages ?? 1));

    $animeList = $items ?? [];

    $getAnimeImage = function ($anime) {
        return data_get($anime, 'images.webp.large_image_url')
            ?? data_get($anime, 'images.jpg.large_image_url')
            ?? data_get($anime, 'images.jpg.image_url')
            ?? data_get($anime, 'image_url')
            ?? data_get($anime, 'poster')
            ?? null;
    };

    $getAnimeTitle = function ($anime) {
        return data_get($anime, 'title')
            ?? data_get($anime, 'title_english')
            ?? data_get($anime, 'name')
            ?? 'Unknown Anime';
    };

    $getAnimeId = function ($anime) {
        return data_get($anime, 'mal_id')
            ?? data_get($anime, 'id');
    };

    $getAnimeScore = function ($anime) {
        return data_get($anime, 'score')
            ?? data_get($anime, 'rating')
            ?? null;
    };

    $getAnimeYear = function ($anime) {
        return data_get($anime, 'year')
            ?? data_get($anime, 'aired.prop.from.year')
            ?? data_get($anime, 'season_year')
            ?? null;
    };
@endphp

<div class="min-h-screen bg-[#09090f]">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Back link --}}
        <a href="{{ route('anime.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-[#a78bfa] transition hover:text-[#c4b5fd]">
            <span>&larr;</span>
            Back to Anime
        </a>

        {{-- Category heading --}}
        <div class="my-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#a78bfa]">
                ENTERTAINMENT+ / ANIME
            </p>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                {{ $title ?? 'Anime Collection' }}
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Discover anime series, movies and more.
            </p>
        </div>

        {{-- Category filters --}}
        <div class="mb-8 flex flex-wrap gap-2 border-b border-white/10 pb-5">
            @php
                $categories = [
                    'popular' => 'Popular',
                    'top' => 'Top Rated',
                    'airing' => 'Currently Airing',
                    'upcoming' => 'Upcoming',
                ];
            @endphp

            @foreach ($categories as $key => $label)
                <a href="{{ route('anime.category', ['category' => $key]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition
                   {{ ($category ?? '') === $key
                        ? 'bg-[#a78bfa] text-[#09090f]'
                        : 'bg-[#171421] text-gray-300 hover:bg-[#29213b] hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- API error --}}
        @if (!empty($apiErrors))
            <div class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                Unable to load some anime data. Please try again later.
            </div>
        @endif

        {{-- Anime grid --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7">

            @forelse ($animeList as $anime)

                @php
                    $id = $getAnimeId($anime);
                    $image = $getAnimeImage($anime);
                    $animeTitle = $getAnimeTitle($anime);
                    $score = $getAnimeScore($anime);
                    $year = $getAnimeYear($anime);
                @endphp

                <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:shadow-lg hover:shadow-purple-950/20">

                    {{-- Poster --}}
                    <a href="{{ $id ? route('anime.show', ['id' => $id]) : '#' }}"
                       class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]">

                        @if ($image)
                            <img src="{{ $image }}"
                                 alt="{{ $animeTitle }}"
                                 loading="lazy"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center p-3 text-center text-sm text-gray-500">
                                No Poster Available
                            </div>
                        @endif

                        {{-- Rating --}}
                        @if ($score !== null && $score !== '')
                            <div class="absolute right-2 top-2 rounded-md bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400 backdrop-blur">
                                ★ {{ number_format((float) $score, 1) }}
                            </div>
                        @endif

                        {{-- Anime badge --}}
                        <div class="absolute bottom-2 left-2 rounded bg-[#a78bfa] px-2 py-1 text-[10px] font-black uppercase tracking-wide text-[#09090f]">
                            Anime
                        </div>
                    </a>

                    {{-- Card information --}}
                    <div class="p-3">

                        <h2 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-white transition group-hover:text-[#c4b5fd]">
                            {{ $animeTitle }}
                        </h2>

                        <div class="mt-2 flex items-center justify-between gap-2 text-xs text-gray-400">
                            <span>{{ $year ?: 'Anime Series' }}</span>

                            @if (data_get($anime, 'episodes'))
                                <span>{{ data_get($anime, 'episodes') }} Episodes</span>
                            @endif
                        </div>

                        <a href="{{ $id ? route('anime.show', ['id' => $id]) : '#' }}"
                           class="mt-3 block rounded-lg bg-[#29213b] py-2 text-center text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                            View Details
                        </a>

                    </div>
                </div>

            @empty

                <div class="col-span-full rounded-xl border border-white/10 bg-[#13131e] px-5 py-16 text-center">
                    <div class="mb-3 text-4xl">🎌</div>

                    <h2 class="text-lg font-bold text-white">
                        No Anime Found
                    </h2>

                    <p class="mt-2 text-sm text-gray-400">
                        No anime titles are available in this category right now.
                    </p>

                    <a href="{{ route('anime.index') }}"
                       class="mt-5 inline-block rounded-lg bg-[#a78bfa] px-5 py-2 text-sm font-bold text-[#09090f] hover:bg-[#c4b5fd]">
                        Browse Anime
                    </a>
                </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        {{-- Pagination --}}
{{-- Pagination --}}
@if ($lastPage > 1)

    <div class="mt-10 mb-8">

        <div class="flex flex-wrap items-center justify-center gap-2">

            {{-- Previous --}}
            @if ($currentPage > 1)
                <a href="{{ route('anime.category', ['category' => $category, 'page' => $currentPage - 1]) }}"
                   class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                    &larr; Previous
                </a>
            @endif

            {{-- First page --}}
            <a href="{{ route('anime.category', ['category' => $category, 'page' => 1]) }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold transition
               {{ $currentPage === 1
                    ? 'bg-[#a78bfa] text-[#09090f]'
                    : 'bg-[#1c1a29] text-white hover:bg-[#a78bfa] hover:text-[#09090f]' }}">
                1
            </a>

            {{-- Left dots --}}
            @if ($currentPage > 4)
                <span class="px-2 text-gray-500">...</span>
            @endif

            {{-- Middle pages --}}
            @for ($number = max(2, $currentPage - 1); $number <= min($lastPage - 1, $currentPage + 1); $number++)

                <a href="{{ route('anime.category', ['category' => $category, 'page' => $number]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition
                   {{ $currentPage === $number
                        ? 'bg-[#a78bfa] text-[#09090f]'
                        : 'bg-[#1c1a29] text-white hover:bg-[#a78bfa] hover:text-[#09090f]' }}">
                    {{ $number }}
                </a>

            @endfor

            {{-- Right dots --}}
            @if ($currentPage < $lastPage - 3)
                <span class="px-2 text-gray-500">...</span>
            @endif

            {{-- Last page --}}
            @if ($lastPage > 1)
                <a href="{{ route('anime.category', ['category' => $category, 'page' => $lastPage]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition
                   {{ $currentPage === $lastPage
                        ? 'bg-[#a78bfa] text-[#09090f]'
                        : 'bg-[#1c1a29] text-white hover:bg-[#a78bfa] hover:text-[#09090f]' }}">
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Next --}}
            @if ($currentPage < $lastPage)
                <a href="{{ route('anime.category', ['category' => $category, 'page' => $currentPage + 1]) }}"
                   class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                    Next &rarr;
                </a>
            @endif

        </div>

        {{-- Page information --}}
        <p class="mt-4 text-center text-xs text-gray-500">
            Page {{ $currentPage }} of {{ $lastPage }}
        </p>

    </div>

@endif

    </div>
</div>

@endsection
