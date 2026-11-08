@extends('layouts.app')

@section('title', ($anime['title'] ?? 'Anime Details') . ' | ENTERTAINMENT+')

@section('description', 'Explore anime details, ratings, episodes, genres and recommendations.')

@section('content')

    @php
        $id = $anime['mal_id'] ?? ($anime['id'] ?? null);

        $title = $anime['title'] ?? 'Unknown Anime';

        $englishTitle = $anime['title_english'] ?? null;

        $poster =
            $anime['images']['jpg']['large_image_url'] ??
            ($anime['images']['webp']['large_image_url'] ?? ($anime['image_url'] ?? ($anime['image'] ?? null)));

        $backdrop =
            $anime['trailer']['images']['maximum_image_url'] ??
            ($anime['trailer']['images']['large_image_url'] ?? $poster);

        $releaseDate = $anime['aired']['from'] ?? ($anime['aired']['string'] ?? null);

        $year =
            $anime['year'] ??
            ($anime['aired']['prop']['from']['year'] ?? (null ?? ($releaseDate ? substr($releaseDate, 0, 4) : null)));

        $score = $anime['score'] ?? null;

        $episodes = $anime['episodes'] ?? null;

        $status = $anime['status'] ?? 'Unknown';

        $synopsis = $anime['synopsis'] ?? ($anime['description'] ?? 'No description is available for this anime yet.');

        $genres = $anime['genres'] ?? [];

        $studios = $anime['studios'] ?? [];

        $producers = $anime['producers'] ?? [];

        $themes = $anime['themes'] ?? [];

        $demographics = $anime['demographics'] ?? [];

        $recommendations = $recommendations ?? [];

        $trailer = $anime['trailer']['url'] ?? null;

        $trailerId = $anime['trailer']['youtube_id'] ?? null;

        $externalUrl = $anime['url'] ?? null;
    @endphp

    {{-- ANIME BACKDROP --}}
    <section class="relative">

        @if ($backdrop)
            <div class="absolute inset-0">
                <img src="{{ $backdrop }}" alt="" class="h-full w-full object-cover opacity-25">

                <div class="absolute inset-0 bg-gradient-to-t from-[#09090f] via-[#09090f]/85 to-[#09090f]/30"></div>
            </div>
        @endif

        <div class="relative mx-auto max-w-7xl px-6 py-12 md:py-20">

            {{-- BACK NAVIGATION --}}
            <div class="mb-8">
                <a href="{{ route('anime.index') }}" class="text-gray-300 transition hover:text-[#c4b5fd]">
                    ← Back to Anime
                </a>
            </div>

            <div class="grid grid-cols-1 items-start gap-10 md:grid-cols-3">

                {{-- ANIME POSTER --}}
                <div class="md:col-span-1">

                    @if ($poster)
                        <img src="{{ $poster }}" alt="{{ $title }}"
                            class="mx-auto w-full max-w-sm rounded-xl shadow-2xl">
                    @else
                        <div
                            class="mx-auto flex aspect-[2/3] max-w-sm items-center justify-center rounded-xl bg-[#13131e] text-gray-500">
                            No poster available
                        </div>
                    @endif

                    {{-- WATCH BUTTON --}}
                    @if ($id)
                        <a href="{{ route('watch.anime', ['id' => $id]) }}"
                            class="mt-6 block w-full rounded-lg bg-[#a78bfa] px-6 py-3 text-center font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">

                            ▶ Watch Anime
                        </a>
                    @endif

                </div>

                {{-- ANIME INFORMATION --}}

                {{-- ANIME INFORMATION --}}
                <div class="min-w-0 pt-2 md:col-span-2">

                    <p class="mb-3 text-sm font-bold uppercase tracking-widest text-[#a78bfa]">
                        Anime Series
                    </p>

                    <h1 class="mb-3 break-words text-3xl font-extrabold leading-tight sm:text-4xl lg:text-6xl">
                        {{ $title }}
                    </h1>

                    @if ($englishTitle && $englishTitle !== $title)
                        <p class="mb-5 break-words text-lg text-gray-400 sm:text-xl">
                            {{ $englishTitle }}
                        </p>
                    @endif

                    {{-- ANIME STATISTICS --}}
                    <div class="mb-7 flex flex-wrap items-center gap-x-4 gap-y-3 text-sm text-gray-300 sm:text-base">

                        @if ($year)
                            <span>{{ $year }}</span>
                        @endif

                        @if ($score !== null)
                            <span class="font-bold text-yellow-400">
                                ★ {{ number_format((float) $score, 1) }}/10
                            </span>
                        @endif

                        @if ($episodes)
                            <span>
                                {{ $episodes }} {{ $episodes == 1 ? 'Episode' : 'Episodes' }}
                            </span>
                        @endif

                        @if (!empty($anime['type']))
                            <span>{{ $anime['type'] }}</span>
                        @endif

                        @if (!empty($anime['duration']))
                            <span>{{ $anime['duration'] }}</span>
                        @endif

                    </div>

                    {{-- GENRES --}}
                    @if (count($genres))
                        <div class="mb-7 min-w-0">

                            <h2 class="mb-3 text-lg font-bold">Genres</h2>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($genres as $genre)
                                    <span
                                        class="max-w-full break-words rounded-full border border-[#a78bfa]/40 px-3 py-1 text-sm text-[#c4b5fd]">
                                        {{ $genre['name'] ?? ($genre['title'] ?? 'Anime') }}
                                    </span>
                                @endforeach
                            </div>

                        </div>
                    @endif

                    {{-- TAGLINE --}}
                    @if (!empty($anime['tagline']))
                        <p class="mb-5 break-words text-lg italic text-gray-300 sm:text-xl">
                            "{{ $anime['tagline'] }}"
                        </p>
                    @endif

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-8 flex min-w-0 flex-wrap gap-3">

                        @if ($id)
                            <a href="{{ route('watch.anime', ['id' => $id]) }}"
                                class="inline-flex max-w-full items-center justify-center rounded-lg bg-[#a78bfa] px-5 py-3 text-center font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
                                ▶ Watch Now
                            </a>
                        @endif

                        @if ($trailerId)
                            <a href="https://www.youtube.com/watch?v={{ $trailerId }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex max-w-full items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-center font-bold text-white transition hover:bg-red-700">
                                ▶ Watch Trailer
                            </a>
                        @elseif($trailer)
                            <a href="{{ $trailer }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex max-w-full items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-center font-bold text-white transition hover:bg-red-700">
                                ▶ Watch Trailer
                            </a>
                        @endif

                        @if ($externalUrl)
                            <a href="{{ $externalUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex max-w-full items-center justify-center rounded-lg bg-gray-800 px-5 py-3 text-center font-bold text-white transition hover:bg-gray-700">
                                More Information ↗
                            </a>
                        @endif

                    </div>

                    {{-- SYNOPSIS --}}
                    {{-- <h2 class="mb-3 text-2xl font-bold">Synopsis</h2> --}}

                    {{-- <p class="mb-7 break-words whitespace-pre-line text-sm leading-7 text-gray-300 sm:text-base sm:leading-8">
        {{ $synopsis }}
    </p> --}}

                    {{-- ADDITIONAL DETAILS --}}
                    <div class="mb-8 grid min-w-0 grid-cols-1 gap-5 sm:grid-cols-2">

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="break-words font-semibold">{{ $status }}</p>
                        </div>

                        @if (!empty($anime['source']))
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">Source</p>
                                <p class="break-words font-semibold">{{ $anime['source'] }}</p>
                            </div>
                        @endif

                        @if (!empty($anime['season']))
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">Season</p>
                                <p class="break-words font-semibold">
                                    {{ ucfirst($anime['season']) }} {{ $year }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($anime['rating']))
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">Age Rating</p>
                                <p class="break-words font-semibold">{{ $anime['rating'] }}</p>
                            </div>
                        @endif

                        @if (!empty($anime['rank']))
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">Rank</p>
                                <p class="font-semibold">#{{ $anime['rank'] }}</p>
                            </div>
                        @endif

                        @if (!empty($anime['popularity']))
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">Popularity</p>
                                <p class="font-semibold">#{{ $anime['popularity'] }}</p>
                            </div>
                        @endif

                    </div>

                    {{-- STUDIOS --}}
                    @if (count($studios))
                        <div class="mb-6 min-w-0">
                            <p class="mb-2 text-sm text-gray-500">Studios</p>

                            <p class="break-words font-semibold">
                                @foreach ($studios as $studio)
                                    {{ $studio['name'] ?? '' }}@if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    @endif

                    {{-- PRODUCERS --}}
                    @if (count($producers))
                        <div class="mb-6 min-w-0">
                            <p class="mb-2 text-sm text-gray-500">Producers</p>

                            <p class="break-words font-semibold">
                                @foreach ($producers as $producer)
                                    {{ $producer['name'] ?? '' }}@if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    @endif

                    {{-- THEMES --}}
                    @if (count($themes))
                        <div class="mb-6 min-w-0">
                            <p class="mb-2 text-sm text-gray-500">Themes</p>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($themes as $theme)
                                    <span
                                        class="max-w-full break-words rounded-md bg-[#13131e] px-3 py-1 text-sm text-gray-300">
                                        {{ $theme['name'] ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif



                </div>

            </div>
        </div>
    </section>

    {{-- EPISODE & SEASON INFORMATION --}}
    <section class="mt-8 rounded-2xl border border-white/10 bg-[#13131e] p-5 sm:p-6">

        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                Anime Information
            </p>

            <h2 class="mt-1 text-xl font-bold text-white">
                Episodes & Season
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

            <div class="rounded-xl border border-white/5 bg-[#09090f] p-4">
                <p class="text-xs text-gray-400">Total Episodes</p>
                <p class="mt-2 text-xl font-bold text-white">
                    {{ $episodes ?: 'Unknown' }}
                </p>
            </div>

            <div class="rounded-xl border border-white/5 bg-[#09090f] p-4">
                <p class="text-xs text-gray-400">Season</p>
                <p class="mt-2 text-lg font-bold capitalize text-white">
                    {{ $anime['season'] ?? 'Unknown' }}
                </p>
            </div>

            <div class="rounded-xl border border-white/5 bg-[#09090f] p-4">
                <p class="text-xs text-gray-400">Release Year</p>
                <p class="mt-2 text-xl font-bold text-white">
                    {{ $year ?: 'Unknown' }}
                </p>
            </div>

            <div class="rounded-xl border border-white/5 bg-[#09090f] p-4">
                <p class="text-xs text-gray-400">Airing Status</p>
                <p class="mt-2 text-sm font-bold text-[#a78bfa]">
                    {{ $status ?: 'Unknown' }}
                </p>
            </div>

        </div>

        <div class="mt-5 border-t border-white/10 pt-4">
            <p class="text-sm text-gray-400">
                Release Date:
                <span class="font-medium text-white">
                    {{ $releaseDate ?: 'Not available' }}
                </span>
            </p>
        </div>

    </section>

    {{-- EPISODE LIST --}}
    <section class="mx-auto mt-10 max-w-7xl px-4">

        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">
                    Episodes
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Browse available episode information
                </p>
            </div>

            <span class="rounded-full bg-[#a78bfa]/10 px-3 py-1 text-sm font-semibold text-[#a78bfa]">
                {{ count($episodeList ?? []) }} Episodes
            </span>
        </div>

        @if (!empty($episodeList))

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($episodeList as $episode)
                    @php
                        $episodeNumber = $episode['mal_id'] ?? $loop->iteration;
                        $episodeTitle = $episode['title'] ?? 'Untitled Episode';
                        $episodeJapanese = $episode['title_japanese'] ?? null;
                        $duration = isset($episode['duration']) ? round($episode['duration'] / 60) . ' min' : null;
                        $aired = data_get($episode, 'aired.from');
                    @endphp

                    <div class="rounded-xl border border-white/10 bg-[#13131e] p-4 transition hover:border-[#a78bfa]/50">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[#a78bfa]">
                                    Episode {{ $loop->iteration }}
                                </p>

                                <h3 class="mt-2 font-semibold text-white">
                                    {{ $episodeTitle }}
                                </h3>

                                @if ($episodeJapanese)
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $episodeJapanese }}
                                    </p>
                                @endif

                                @if (!empty($episode['synopsis']))
                                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-gray-400">
                                        {{ $episode['synopsis'] }}
                                    </p>
                                @endif
                            </div>

                            <span class="shrink-0 rounded-lg bg-white/5 px-2 py-1 text-xs text-gray-400">
                                #{{ $episodeNumber }}
                            </span>

                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-400">

                            @if (isset($episode['score']) && $episode['score'] !== null)
                                <span class="rounded-md bg-yellow-500/10 px-2 py-1 text-yellow-400">
                                    ★ {{ number_format((float) $episode['score'], 2) }}
                                </span>
                            @endif

                            @if ($duration)
                                <span class="rounded-md bg-white/5 px-2 py-1">
                                    {{ $duration }}
                                </span>
                            @endif

                            @if ($aired)
                                <span class="rounded-md bg-white/5 px-2 py-1">
                                    {{ \Illuminate\Support\Carbon::parse($aired)->format('M d, Y') }}
                                </span>
                            @endif

                            @if (!empty($episode['filler']))
                                <span class="rounded-md bg-amber-500/10 px-2 py-1 text-amber-400">
                                    Filler
                                </span>
                            @endif

                            @if (!empty($episode['recap']))
                                <span class="rounded-md bg-blue-500/10 px-2 py-1 text-blue-400">
                                    Recap
                                </span>
                            @endif

                        </div>

                    </div>
                @endforeach

            </div>

            @if (data_get($episodePagination, 'has_next_page'))
                <p class="mt-5 text-center text-sm text-gray-400">
                    More episodes are available on the next page.
                </p>
            @endif
        @else
            <div class="rounded-xl border border-white/10 bg-[#13131e] p-8 text-center">
                <p class="text-gray-400">
                    Episode information is currently unavailable.
                </p>
            </div>

        @endif

    </section>

    {{-- RECOMMENDED ANIME --}}
    @if (count($recommendations))
        <section class="mx-auto max-w-7xl px-6 py-12">

            @include('partials.media-section', [
                'title' => 'You May Also Like',
                'items' => $recommendations,
                'source' => 'anime',
                'type' => 'anime',
            ])

        </section>
    @endif

@endsection
