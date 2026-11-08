
@extends('layouts.app')

@section('title', ucfirst($slug) . ' ' . ($type === 'movie' ? 'Movies' : 'TV Shows') . ' | ENTERTAINMENT+')

@section('description', 'Explore ' . ucfirst($slug) . ' ' . ($type === 'movie' ? 'movies' : 'TV shows') . ' on ENTERTAINMENT+.')

@section('content')

@php
    $currentPage = max(1, (int) ($page ?? 1));
    $lastPage = max(1, (int) ($totalPages ?? 1));

    $isMovie = $type === 'movie';

    $categoryNames = [
        'trending' => 'Trending',
        'popular' => 'Popular',
        'action' => 'Action',
        'comedy' => 'Comedy',
        'bollywood' => 'Bollywood',
        'hollywood' => 'Hollywood',
        'korean' => 'Korean Drama',
    ];

    $categoryName = $categoryNames[$slug] ?? ucfirst($slug);
@endphp

<div class="min-h-screen bg-[#09090f]">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- BACK LINK --}}
        <a
            href="{{ route('movies.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-[#a78bfa] transition hover:text-[#c4b5fd]"
        >
            &larr; Back to Movies
        </a>

        {{-- HEADING --}}
        <div class="my-8">

            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#a78bfa]">
                ENTERTAINMENT+ / {{ $isMovie ? 'MOVIES' : 'TV SHOWS' }}
            </p>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                {{ $categoryName }} {{ $isMovie ? 'Movies' : 'TV Shows' }}
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Discover {{ strtolower($categoryName) }} {{ $isMovie ? 'movies' : 'television series' }}.
            </p>

        </div>

        {{-- CATEGORY NAVIGATION --}}
        <div class="mb-8 flex flex-wrap gap-2 border-b border-white/10 pb-5">

            <a
                href="{{ route('movies.index') }}"
                class="rounded-lg bg-[#171421] px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-[#29213b] hover:text-white"
            >
                All Movies
            </a>

            <a
                href="{{ route('movies.category', ['type' => 'movie', 'slug' => 'trending']) }}"
                class="rounded-lg {{ $isMovie && $slug === 'trending' ? 'bg-[#a78bfa] text-[#09090f]' : 'bg-[#171421] text-gray-300 hover:bg-[#29213b] hover:text-white' }} px-4 py-2 text-sm font-semibold transition"
            >
                Trending Movies
            </a>

            <a
                href="{{ route('movies.category', ['type' => 'movie', 'slug' => 'popular']) }}"
                class="rounded-lg {{ $isMovie && $slug === 'popular' ? 'bg-[#a78bfa] text-[#09090f]' : 'bg-[#171421] text-gray-300 hover:bg-[#29213b] hover:text-white' }} px-4 py-2 text-sm font-semibold transition"
            >
                Popular Movies
            </a>

            <a
                href="{{ route('movies.category', ['type' => 'tv', 'slug' => 'trending']) }}"
                class="rounded-lg {{ !$isMovie && $slug === 'trending' ? 'bg-[#a78bfa] text-[#09090f]' : 'bg-[#171421] text-gray-300 hover:bg-[#29213b] hover:text-white' }} px-4 py-2 text-sm font-semibold transition"
            >
                Trending TV
            </a>

            <a
                href="{{ route('movies.category', ['type' => 'tv', 'slug' => 'popular']) }}"
                class="rounded-lg {{ !$isMovie && $slug === 'popular' ? 'bg-[#a78bfa] text-[#09090f]' : 'bg-[#171421] text-gray-300 hover:bg-[#29213b] hover:text-white' }} px-4 py-2 text-sm font-semibold transition"
            >
                Popular TV
            </a>

        </div>

        {{-- API ERRORS --}}
        @if(!empty($apiErrors))
            <div class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ $apiErrors[0] }}
            </div>
        @endif

        {{-- RESULTS COUNT --}}
        <div class="mb-5 flex items-center justify-between">

            <h2 class="text-lg font-bold text-white">
                {{ $categoryName }} Collection
            </h2>

            <span class="text-xs text-gray-500">
                Page {{ $currentPage }} of {{ $lastPage }}
            </span>

        </div>

        {{-- MOVIE GRID --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7">

            @forelse($items as $item)

                @php
                    $itemId = $item['id'] ?? null;
                    $title = $item['title'] ?? $item['name'] ?? 'Untitled';
                    $poster = $item['poster_path'] ?? null;
                    $date = $item['release_date'] ?? $item['first_air_date'] ?? null;
                    $year = $date ? substr($date, 0, 4) : null;
                    $rating = $item['vote_average'] ?? null;
                @endphp

                <div class="group min-w-0 overflow-hidden rounded-xl border border-white/5 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/50 hover:shadow-xl hover:shadow-purple-950/30">

                    {{-- POSTER --}}
                    <a
                        href="{{ $itemId ? route('movies.show', ['type' => $type, 'id' => $itemId]) : '#' }}"
                        class="relative block aspect-[2/3] overflow-hidden bg-[#1c1a29]"
                    >

                        @if($poster)

                            <img
                                src="https://image.tmdb.org/t/p/w342{{ $poster }}"
                                alt="{{ $title }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                            >

                        @else

                            <div class="flex h-full items-center justify-center p-3 text-center text-sm text-gray-500">
                                No Poster Available
                            </div>

                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/10 opacity-70 transition group-hover:opacity-100"></div>

                        {{-- RATING --}}
                        @if($rating !== null)
                            <div class="absolute right-2 top-2 rounded-md border border-white/10 bg-black/80 px-2 py-1 text-xs font-bold text-yellow-400 backdrop-blur">
                                ★ {{ number_format((float) $rating, 1) }}
                            </div>
                        @endif

                        {{-- TYPE --}}
                        <span class="absolute bottom-2 left-2 rounded-md bg-[#a78bfa] px-2 py-1 text-[10px] font-black uppercase tracking-wide text-[#09090f]">
                            {{ $isMovie ? 'Movie' : 'TV Series' }}
                        </span>

                        {{-- HOVER ICON --}}
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
                            {{ $title }}
                        </h3>

                        <div class="mt-2 flex items-center justify-between gap-2 text-xs text-gray-400">

                            <span>{{ $year ?? 'Release unknown' }}</span>

                            @if($rating !== null)
                                <span class="text-yellow-400">
                                    ★ {{ number_format((float) $rating, 1) }}
                                </span>
                            @endif

                        </div>

                        <a
                            href="{{ $itemId ? route('movies.show', ['type' => $type, 'id' => $itemId]) : '#' }}"
                            class="mt-3 block rounded-lg bg-[#29213b] py-2 text-center text-xs font-bold text-[#c4b5fd] transition hover:bg-[#a78bfa] hover:text-[#09090f]"
                        >
                            View Details
                        </a>

                    </div>

                </div>

            @empty

                <div class="col-span-full rounded-xl border border-white/10 bg-[#13131e] px-5 py-16 text-center">

                    <div class="mb-3 text-4xl">🎬</div>

                    <h2 class="text-lg font-bold text-white">
                        No Titles Found
                    </h2>

                    <p class="mt-2 text-sm text-gray-400">
                        No titles are currently available in this category.
                    </p>

                    <a
                        href="{{ route('movies.index') }}"
                        class="mt-5 inline-block rounded-lg bg-[#a78bfa] px-5 py-2 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]"
                    >
                        Browse Movies
                    </a>

                </div>

            @endforelse

        </div>


        {{-- PAGINATION --}}
        @if($lastPage > 1)

            <div class="mb-8 mt-10 flex flex-wrap items-center justify-center gap-2">

                @if($currentPage > 1)
                    <a
                        href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                        class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]"
                    >
                        Previous
                    </a>
                @endif

                @for($number = max(1, $currentPage - 2); $number <= min($lastPage, $currentPage + 2); $number++)

                    <a
                        href="{{ request()->fullUrlWithQuery(['page' => $number]) }}"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $currentPage === $number ? 'bg-[#a78bfa] text-[#09090f]' : 'bg-[#1c1a29] text-white hover:bg-[#a78bfa] hover:text-[#09090f]' }}"
                    >
                        {{ $number }}
                    </a>

                @endfor

                @if($currentPage < $lastPage)
                    <a
                        href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                        class="rounded-lg bg-[#1c1a29] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#a78bfa] hover:text-[#09090f]"
                    >
                        Next
                    </a>
                @endif

            </div>

        @endif

    </div>

</div>

@endsection
