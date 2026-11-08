
@extends('layouts.app')

@section('title', ($anime['title'] ?? 'Anime Watch') . ' | ENTERTAINMENT+')

@section('content')

@php
    $animeId = $anime['mal_id'] ?? $anime['id'] ?? null;
    $title = $anime['title'] ?? 'Unknown Anime';

    $selectedTitle = $selectedEpisode['title'] ?? 'Select Episode';
@endphp

<div class="mx-auto max-w-7xl px-4 py-8">

    {{-- BREADCRUMB --}}
    <div class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-[#a78bfa]">
            Home
        </a>
        <span>/</span>
        <a href="{{ route('anime.show', $animeId) }}" class="hover:text-[#a78bfa]">
            {{ $title }}
        </a>
        <span>/</span>
        <span class="text-[#a78bfa]">Watch</span>
    </div>

    {{-- PLAYER --}}
    <section class="overflow-hidden rounded-2xl border border-white/10 bg-[#13131e]">

        <div class="relative aspect-video bg-black">

            @if($embedUrl)

                <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $title }} - {{ $selectedTitle }}"
                    class="absolute inset-0 h-full w-full"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allow="autoplay; fullscreen; picture-in-picture"
                ></iframe>

            @else

                <div class="flex h-full items-center justify-center p-6 text-center">
                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Episode Unavailable
                        </h2>

                        <p class="mt-3 text-sm text-gray-400">
                            No episode information is available for this anime.
                        </p>
                    </div>
                </div>

            @endif

        </div>

        {{-- PLAYER INFORMATION --}}
        <div class="p-5 sm:p-7">

            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">

                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[#a78bfa]">
                        Now Watching
                    </p>

                    <h1 class="text-2xl font-black text-white">
                        {{ $title }}
                    </h1>

                    <p class="mt-2 text-sm text-gray-400">
                        Episode {{ $episodeNumber ?? '-' }}:
                        {{ $selectedTitle }}
                    </p>
                </div>

                <a href="{{ route('anime.show', $animeId) }}"
                   class="rounded-lg border border-white/10 px-5 py-3 text-center text-sm font-semibold text-white transition hover:border-[#a78bfa] hover:text-[#a78bfa]">
                    Anime Details
                </a>

            </div>

        </div>

    </section>

    {{-- PREVIOUS / NEXT EPISODE --}}
<div class="mt-5 flex items-center justify-between gap-4">

    @if($previousEpisode)
        <a href="{{ route('watch.anime', $animeId) }}?episode={{ $previousEpisode['mal_id'] }}&episode_page={{ $episodePage }}"
           class="flex items-center gap-2 rounded-lg border border-white/10 bg-[#13131e] px-5 py-3 text-sm font-semibold text-white transition hover:border-[#a78bfa] hover:text-[#a78bfa]">

            <span>←</span>
            Previous Episode
        </a>
    @else
        <span></span>
    @endif

    @if($nextEpisode)
        <a href="{{ route('watch.anime', $animeId) }}?episode={{ $nextEpisode['mal_id'] }}&episode_page={{ $episodePage }}"
           class="flex items-center gap-2 rounded-lg bg-[#a78bfa] px-5 py-3 text-sm font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">

            Next Episode
            <span>→</span>
        </a>
    @endif

</div>

    {{-- EPISODE NAVIGATION --}}
    <section class="mt-8">

        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">
                Episodes
            </h2>

            <span class="text-sm text-gray-400">
                {{ count($episodeList) }} episodes on this page
            </span>
        </div>

        @if(!empty($episodeList))

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($episodeList as $episode)

                    @php
                        $number = (int) ($episode['episode'] ?? $loop->iteration);
                        $episodeId = $episode['mal_id'] ?? null;
                        $episodeTitle = $episode['title'] ?? 'Untitled Episode';

                        $isSelected = $selectedEpisode
                            && (int) ($selectedEpisode['mal_id'] ?? 0)
                            === (int) $episodeId;
                    @endphp

                    <a href="{{ route('watch.anime', $animeId) }}?episode={{ $episodeId }}&episode_page={{ $episodePage }}"
                       class="rounded-xl border p-4 transition
                       {{ $isSelected
                            ? 'border-[#a78bfa] bg-[#a78bfa]/10'
                            : 'border-white/10 bg-[#13131e] hover:border-[#a78bfa]/50' }}">

                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-[#a78bfa]">
                                    Episode {{ $number }}
                                </p>

                                <h3 class="mt-2 font-semibold text-white">
                                    {{ $episodeTitle }}
                                </h3>
                            </div>

                            @if($isSelected)
                                <span class="rounded-md bg-[#a78bfa] px-2 py-1 text-xs font-bold text-[#09090f]">
                                    Playing
                                </span>
                            @else
                                <span class="text-gray-500">▶</span>
                            @endif

                        </div>

                        @if(!empty($episode['duration']))
                            <p class="mt-3 text-xs text-gray-400">
                                {{ round($episode['duration'] / 60) }} min
                            </p>
                        @endif

                    </a>

                @endforeach

            </div>

            {{-- EPISODE PAGINATION --}}
            @php
                $lastPage = (int) data_get(
                    $episodePagination,
                    'last_visible_page',
                    1
                );
            @endphp

            @if($lastPage > 1)

                <div class="mt-7 flex items-center justify-center gap-4">

                    @if($episodePage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['episode_page' => $episodePage - 1]) }}"
                           class="rounded-lg border border-white/10 px-4 py-2 text-sm text-white hover:border-[#a78bfa]">
                            ← Previous
                        </a>
                    @endif

                    <span class="text-sm text-gray-400">
                        Page {{ $episodePage }} of {{ $lastPage }}
                    </span>

                    @if($episodePage < $lastPage)
                        <a href="{{ request()->fullUrlWithQuery(['episode_page' => $episodePage + 1]) }}"
                           class="rounded-lg bg-[#a78bfa] px-4 py-2 text-sm font-semibold text-[#09090f]">
                            Next →
                        </a>
                    @endif

                </div>

            @endif

        @else

            <p class="rounded-xl border border-white/10 bg-[#13131e] p-6 text-center text-gray-400">
                No episodes are currently available.
            </p>

        @endif

    </section>

    {{-- PROVIDER NOTICE --}}
    <div class="mt-8 rounded-xl border border-[#a78bfa]/20 bg-[#a78bfa]/5 p-4">
        <p class="text-sm leading-relaxed text-gray-300">
            <span class="font-bold text-[#a78bfa]">Playback information:</span>
            AnimeEmbed is an external embedded player. Playback depends on its
            availability, supported anime IDs, and authorization to distribute
            the selected content.
        </p>
    </div>

</div>

@endsection
