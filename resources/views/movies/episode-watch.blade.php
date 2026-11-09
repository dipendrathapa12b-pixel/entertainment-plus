@extends('layouts.app')

@php
    $tvTitle = $tv['name'] ?? 'TV Series';
    $episodeName = $episode['name'] ?? 'Episode';
    $seasonNumber = $season['season_number'] ?? 1;
    $episodeNumber = $episode['episode_number'] ?? 1;

    $still = !empty($episode['still_path']) ? 'https://image.tmdb.org/t/p/w1280' . $episode['still_path'] : null;
@endphp

@section('title', $tvTitle . ' - ' . $episodeName . ' | ENTERTAINMENT+')

@section('content')

    <section class="min-h-screen bg-[#09090f] px-4 py-10 text-white">

        <div class="mx-auto max-w-6xl">

            <a href="{{ route('movies.tv.episodes', ['id' => $tv['id']]) }}"
                class="mb-6 inline-block text-sm text-gray-400 hover:text-[#c4b5fd]">
                ← Back to Episodes
            </a>

            <h1 class="mb-2 text-3xl font-extrabold">
                {{ $tvTitle }}
            </h1>

            <h2 class="mb-6 text-xl text-[#c4b5fd]">
                Season {{ $seasonNumber }} · Episode {{ $episodeNumber }}:
                {{ $episodeName }}
            </h2>


            {{-- STELLAR VIDEO PLAYER --}}
            <div class="overflow-hidden rounded-xl border border-white/10 bg-black">

                <div class="relative aspect-video w-full">

                    <iframe
                        src="https://stellar.rip/en/watch/embed/tv/{{ $tv['id'] }}-{{ $seasonNumber }}-{{ $episodeNumber }}?theme=9B8CFF&title=true&poster=true&autoPlay=false&nextButton=true&autoNext=false"
                        title="{{ $tvTitle }} - {{ $episodeName }}" class="absolute inset-0 h-full w-full border-0"
                        allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                </div>

            </div>
            <div class="mt-6 rounded-xl bg-[#13131e] p-5">

                <h3 class="mb-3 text-xl font-bold">Episode Overview</h3>

                <p class="leading-7 text-gray-300">
                    {{ $episode['overview'] ?? 'No episode description available.' }}
                </p>

                <div class="mt-4 flex flex-wrap gap-5 text-sm text-gray-400">

                    <span>
                        Air Date: {{ $episode['air_date'] ?? 'Unknown' }}
                    </span>

                    <span>
                        Runtime: {{ $episode['runtime'] ?? 'Unknown' }} min
                    </span>

                    <span>
                        Rating: {{ $episode['vote_average'] ?? 'N/A' }}/10
                    </span>

                </div>

                {{-- OTHER EPISODES --}}
<div class="mt-8 rounded-xl border border-white/10 bg-[#13131e] p-5">

    <h2 class="mb-5 text-xl font-bold text-white">
        Season {{ $seasonNumber }} Episodes
    </h2>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">

        @foreach(($season['episodes'] ?? []) as $ep)

            <a
                href="{{ route('movies.tv.episode', [
                    'id' => $tv['id'],
                    'season' => $seasonNumber,
                    'episode' => $ep['episode_number']
                ]) }}"
                class="rounded-lg border p-3 transition
                {{ (int) $ep['episode_number'] === (int) $episodeNumber
                    ? 'border-[#a78bfa] bg-[#a78bfa]/20'
                    : 'border-white/10 bg-[#09090f] hover:border-[#a78bfa]' }}"
            >

                <p class="text-sm font-bold text-[#a78bfa]">
                    Episode {{ $ep['episode_number'] }}
                </p>

                <p class="mt-1 line-clamp-2 text-sm text-white">
                    {{ $ep['name'] ?? 'Episode ' . $ep['episode_number'] }}
                </p>

            </a>

        @endforeach

    </div>
</div>

            </div>

        </div>

    </section>

@endsection
