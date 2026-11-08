
@extends('layouts.app')

@section('title', ($genreName ?? 'Anime Genre') . ' Anime | ENTERTAINMENT+')

@section('description', 'Discover anime in the ' . ($genreName ?? 'selected') . ' genre.')

@section('content')

@php
    $currentPage = max(1, (int) ($page ?? 1));
    $lastPage = max(1, (int) ($totalPages ?? 1));

    $getImage = function ($anime) {
        return data_get($anime, 'images.webp.large_image_url')
            ?? data_get($anime, 'images.jpg.large_image_url')
            ?? data_get($anime, 'images.jpg.image_url')
            ?? data_get($anime, 'image_url')
            ?? data_get($anime, 'poster')
            ?? null;
    };

    $getTitle = function ($anime) {
        return data_get($anime, 'title')
            ?? data_get($anime, 'title_english')
            ?? data_get($anime, 'name')
            ?? 'Unknown Anime';
    };

    $getId = function ($anime) {
        return data_get($anime, 'mal_id')
            ?? data_get($anime, 'id');
    };

    $getScore = function ($anime) {
        return data_get($anime, 'score')
            ?? data_get($anime, 'rating');
    };

    $getYear = function ($anime) {
        return data_get($anime, 'year')
            ?? data_get($anime, 'aired.prop.from.year')
            ?? data_get($anime, 'season_year');
    };
@endphp

<div class="min-h-screen bg-[#09090f]">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Back link --}}
        <a href="{{ route('genres.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-[#a78bfa] transition hover:text-[#c4b5fd]">
            &larr; Back to Genres
        </a>

        {{-- Heading --}}
        <div class="my-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#a78bfa]">
                ENTERTAINMENT+ / GENRES
            </p>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                {{ $genreName ?? 'Anime Genre' }} Anime
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Explore anime from the {{ $genreName ?? 'selected' }} genre.
            </p>
        </div>

        {{-- Quick navigation --}}
        <div class="mb-8 flex flex-wrap gap-2 border-b border-white/10 pb-5">
            <a href="{{ route('anime.index') }}"
               class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white">
                All Anime
            </a>

            <a href="{{ route('anime.category', 'popular') }}"
               class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white">
                Popular
            </a>

            <a href="{{ route('genres.index') }}"
               class="rounded-lg bg-[#a78bfa] px-4 py-2 text-sm font-bold text-[#09090f]">
                Genres
            </a>
        </div>

        {{-- API errors --}}
        @if (!empty($apiErrors))
            <div class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ $apiErrors[0] }}
            </div>
        @endif

        {{-- Anime grid --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7">

            @forelse ($items as $anime)

                @php
                    $animeId = $getId($anime);
                    $image = $getImage($anime);
                    $title = $getTitle($anime);
                    $score = $getScore($anime);
                    $year = $getYear($anime);
                    $episodes = data_get($anime, 'episodes');
                @endphp

                <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:shadow-lg hover:shadow-purple-950/20">

                    {{-- Poster --}}
                    <a href="{{ $animeId ? route('anime.show', ['id' => $animeId]) : '#' }}"
                       class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]">

                        @if ($image)
                            <img src="{{ $image }}"
                                 alt="{{ $title }}"
                                 loading="lazy"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center p-3 text-center text-sm text-gray-500">
                                No Poster Available
                            </div>
                        @endif

                        @if ($score !== null && $score !== '')
                            <div class="absolute right-2 top-2 rounded-md bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400 backdrop-blur">
                                ★ {{ number_format((float) $score, 1) }}
                            </div>
                        @endif

                        <span class="absolute bottom-2 left-2 rounded bg-[#a78bfa] px-2 py-1 text-[10px] font-black uppercase tracking-wide text-[#09090f]">
                            Anime
                        </span>
                    </a>

                    {{-- Details --}}
                    <div class="p-3">

                        <h2 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-white transition group-hover:text-[#c4b5fd]">
                            {{ $title }}
                        </h2>

                        <div class="mt-2 flex items-center justify-between gap-2 text-xs text-gray-400">
                            <span>{{ $year ?: 'Anime Series' }}</span>

                            @if ($episodes)
                                <span>{{ $episodes }} Episodes</span>
                            @endif
                        </div>

                        <a href="{{ $animeId ? route('anime.show', ['id' => $animeId]) : '#' }}"
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
                        No anime are currently available in this genre.
                    </p>

                    <a href="{{ route('genres.index') }}"
                       class="mt-5 inline-block rounded-lg bg-[#a78bfa] px-5 py-2 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                        Browse Genres
                    </a>

                </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        @if ($lastPage > 1)

            <div class="mb-8 mt-10 flex flex-wrap items-center justify-center gap-2">

                {{-- Previous --}}
                @if ($currentPage > 1)
                    <a href="{{ route('genres.show', ['id' => $id, 'page' => $currentPage - 1]) }}"
                       class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        Previous
                    </a>
                @endif

                {{-- First page --}}
                @if ($currentPage > 3)
                    <a href="{{ route('genres.show', ['id' => $id, 'page' => 1]) }}"
                       class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        1
                    </a>

                    @if ($currentPage > 4)
                        <span class="px-1 text-gray-500">...</span>
                    @endif
                @endif

                {{-- Pages around current page --}}
                @for ($number = max(1, $currentPage - 2); $number <= min($lastPage, $currentPage + 2); $number++)
                    <a href="{{ route('genres.show', ['id' => $id, 'page' => $number]) }}"
                       class="rounded-lg px-4 py-2 text-sm font-semibold transition
                       {{ $currentPage === $number
                            ? 'bg-[#a78bfa] text-[#09090f]'
                            : 'bg-[#1c1a29] text-white hover:bg-[#a78bfa] hover:text-[#09090f]' }}">
                        {{ $number }}
                    </a>
                @endfor

                {{-- Last page --}}
                @if ($currentPage < $lastPage - 2)
                    @if ($currentPage < $lastPage - 3)
                        <span class="px-1 text-gray-500">...</span>
                    @endif

                    <a href="{{ route('genres.show', ['id' => $id, 'page' => $lastPage]) }}"
                       class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        {{ $lastPage }}
                    </a>
                @endif

                {{-- Next --}}
                @if ($currentPage < $lastPage)
                    <a href="{{ route('genres.show', ['id' => $id, 'page' => $currentPage + 1]) }}"
                       class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]">
                        Next
                    </a>
                @endif

            </div>

        @endif

    </div>
</div>

@endsection
