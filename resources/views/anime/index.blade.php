
@extends('layouts.app')

@section('title', 'Browse Anime | ENTERTAINMENT+')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-10">

    {{-- PAGE HEADER --}}
    <section class="mb-10">
        <span class="mb-3 inline-block rounded-full border border-[#a78bfa]/30 bg-[#a78bfa]/10 px-4 py-1 text-xs font-bold uppercase tracking-widest text-[#c4b5fd]">
            Anime Universe
        </span>

        <h1 class="text-4xl font-black md:text-5xl">
            Explore <span class="text-[#a78bfa]">Anime</span>
        </h1>

        <p class="mt-3 max-w-2xl text-gray-400">
            Discover popular anime, top-rated series, currently airing
            episodes and upcoming adventures.
        </p>
    </section>

    {{-- CATEGORY NAVIGATION --}}
    <section class="mb-12 flex flex-wrap gap-3">

        <a href="{{ route('anime.category', 'popular') }}"
           class="rounded-xl bg-[#a78bfa] px-5 py-3 font-bold text-[#09090f] transition hover:bg-[#c4b5fd]">
            Popular Anime
        </a>

        <a href="{{ route('anime.category', 'top') }}"
           class="rounded-xl border border-white/10 bg-[#13131e] px-5 py-3 font-semibold text-white transition hover:border-[#a78bfa]">
            Top Rated
        </a>

        <a href="{{ route('anime.category', 'airing') }}"
           class="rounded-xl border border-white/10 bg-[#13131e] px-5 py-3 font-semibold text-white transition hover:border-[#a78bfa]">
            Currently Airing
        </a>

        <a href="{{ route('anime.category', 'upcoming') }}"
           class="rounded-xl border border-white/10 bg-[#13131e] px-5 py-3 font-semibold text-white transition hover:border-[#a78bfa]">
            Upcoming
        </a>

        <a href="{{ route('genres.index') }}"
           class="rounded-xl border border-white/10 bg-[#13131e] px-5 py-3 font-semibold text-white transition hover:border-[#a78bfa]">
            Genres
        </a>

    </section>

    {{-- API ERRORS --}}
    @if(!empty($apiErrors))
        <div class="mb-8 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-300">
            @foreach($apiErrors as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- ANIME SECTIONS --}}
    @include('partials.media-section', [
        'title' => 'Popular Anime',
        'items' => $popularAnime,
        'source' => 'anime',
        'type' => 'anime'
    ])

    @include('partials.media-section', [
        'title' => 'Top Rated Anime',
        'items' => $topAnime,
        'source' => 'anime',
        'type' => 'anime'
    ])

    @include('partials.media-section', [
        'title' => 'Currently Airing',
        'items' => $airingAnime,
        'source' => 'anime',
        'type' => 'anime'
    ])

    @include('partials.media-section', [
        'title' => 'Upcoming Anime',
        'items' => $upcomingAnime,
        'source' => 'anime',
        'type' => 'anime'
    ])

</div>

@endsection
