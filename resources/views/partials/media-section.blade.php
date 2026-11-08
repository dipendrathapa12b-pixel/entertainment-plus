
@php
    $sectionId = 'media-' . \Illuminate\Support\Str::slug($title . '-' . $type);

    $items = $items ?? [];
    $source = $source ?? ($type === 'anime' ? 'anime' : 'tmdb');
@endphp

<section class="mb-12">

    {{-- SECTION HEADING --}}
    <div class="mb-5 flex items-center justify-between gap-3">

        <div class="flex items-center gap-3">
            <span class="h-7 w-1 rounded-full bg-[#a78bfa]"></span>

            <h2 class="text-lg font-bold text-white sm:text-xl">
                {{ $title }}
            </h2>
        </div>

        @if($type === 'anime')
            <a href="{{ route('anime.index') }}"
               class="whitespace-nowrap text-xs font-semibold text-[#a78bfa] transition hover:text-[#c4b5fd] sm:text-sm">
                View More →
            </a>
        @else
            <a href="{{ route('movies.index') }}"
               class="whitespace-nowrap text-xs font-semibold text-[#a78bfa] transition hover:text-[#c4b5fd] sm:text-sm">
                View More →
            </a>
        @endif

    </div>

    @if(count($items))

        {{-- SLIDER --}}
        <div class="group/slider relative">

            {{-- LEFT ARROW --}}
            <button
                type="button"
                onclick="document.getElementById('{{ $sectionId }}').scrollBy({ left: -650, behavior: 'smooth' })"
                class="absolute -left-2 top-1/2 z-20 hidden h-11 w-9 -translate-y-1/2 items-center justify-center rounded-lg border border-white/10 bg-[#09090f]/90 text-white shadow-lg transition hover:bg-[#a78bfa] sm:flex">
                ❮
            </button>

            {{-- CARDS --}}
            <div
                id="{{ $sectionId }}"
                class="flex gap-3 overflow-x-auto scroll-smooth pb-4"
                style="scrollbar-width: none;">

                @foreach($items as $item)

                    @php
                        $id = $type === 'anime'
                            ? ($item['mal_id'] ?? $item['id'] ?? null)
                            : ($item['id'] ?? null);

                        $name = $type === 'anime'
                            ? ($item['title'] ?? $item['title_english'] ?? 'Unknown Anime')
                            : ($item['title'] ?? $item['name'] ?? 'Untitled');

                        $poster = null;

                        if ($type === 'anime') {
                            $poster = data_get($item, 'images.jpg.large_image_url')
                                ?? data_get($item, 'images.webp.large_image_url')
                                ?? data_get($item, 'images.jpg.image_url')
                                ?? $item['image'] ?? $item['image_url'] ?? null;
                        } else {
                            $poster = !empty($item['poster_path'])
                                ? 'https://image.tmdb.org/t/p/w342' . $item['poster_path']
                                : null;
                        }

                        $rating = $type === 'anime'
                            ? ($item['score'] ?? null)
                            : ($item['vote_average'] ?? null);

                        $year = $type === 'anime'
                            ? ($item['year'] ?? data_get($item, 'aired.prop.from.year'))
                            : (!empty($item['release_date'])
                                ? substr($item['release_date'], 0, 4)
                                : (!empty($item['first_air_date'])
                                    ? substr($item['first_air_date'], 0, 4)
                                    : null));

                        $detailsUrl = $type === 'anime'
                            ? ($id ? route('anime.show', $id) : '#')
                            : ($id ? route('movies.show', ['type' => $type, 'id' => $id]) : '#');
                    @endphp

                    @if($id)

                        <article
                            class="group/card w-36 flex-shrink-0 overflow-hidden rounded-xl border border-white/10 bg-[#13131e] transition duration-300 hover:-translate-y-1 hover:border-[#a78bfa]/70 hover:shadow-xl hover:shadow-[#a78bfa]/10 sm:w-44 md:w-48">

                            {{-- POSTER --}}
                            <a href="{{ $detailsUrl }}"
                               class="relative block overflow-hidden bg-[#20202d]">

                                @if($poster)
                                    <img
                                        src="{{ $poster }}"
                                        alt="{{ $name }}"
                                        loading="lazy"
                                        class="h-48 w-full object-cover transition duration-500 group-hover/card:scale-105 sm:h-56 md:h-64">
                                @else
                                    <div class="flex h-48 items-center justify-center text-xs text-gray-500 sm:h-56 md:h-64">
                                        No Poster Available
                                    </div>
                                @endif

                                {{-- IMAGE OVERLAY --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-70"></div>

                                {{-- TYPE BADGE --}}
                                <span class="absolute left-2 top-2 rounded-md bg-[#a78bfa] px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide text-[#09090f]">
                                    {{ $type === 'anime' ? 'Anime' : $type }}
                                </span>

                                {{-- RATING --}}
                                @if($rating !== null)
                                    <span class="absolute right-2 top-2 rounded-md border border-white/10 bg-black/80 px-2 py-1 text-[10px] font-bold text-yellow-400">
                                        ★ {{ number_format((float) $rating, 1) }}
                                    </span>
                                @endif

                                {{-- YEAR --}}
                                @if($year)
                                    <span class="absolute bottom-2 left-2 text-[10px] font-semibold text-white">
                                        {{ $year }}
                                    </span>
                                @endif

                            </a>

                            {{-- CARD INFORMATION --}}
                            <div class="p-3">

                                <a href="{{ $detailsUrl }}"
                                   title="{{ $name }}"
                                   class="block min-h-10 text-sm font-bold leading-snug text-white transition group-hover/card:text-[#c4b5fd] line-clamp-2">
                                    {{ $name }}
                                </a>

                                <p class="mt-1 truncate text-[10px] text-gray-500">
                                    {{ $type === 'anime' ? 'Anime Series' : ($type === 'tv' ? 'TV Show' : 'Movie') }}
                                </p>

                                {{-- DETAILS BUTTON --}}
                                <a href="{{ $detailsUrl }}"
                                   class="mt-3 flex w-full items-center justify-center gap-1 rounded-lg border border-[#a78bfa]/30 bg-[#a78bfa]/10 px-2 py-2 text-[11px] font-bold text-[#c4b5fd] transition hover:border-[#a78bfa] hover:bg-[#a78bfa] hover:text-[#09090f]">
                                    View Details
                                    <span>→</span>
                                </a>

                            </div>

                        </article>

                    @endif

                @endforeach

            </div>

            {{-- RIGHT ARROW --}}
            <button
                type="button"
                onclick="document.getElementById('{{ $sectionId }}').scrollBy({ left: 650, behavior: 'smooth' })"
                class="absolute -right-2 top-1/2 z-20 hidden h-11 w-9 -translate-y-1/2 items-center justify-center rounded-lg border border-white/10 bg-[#09090f]/90 text-white shadow-lg transition hover:bg-[#a78bfa] sm:flex">
                ❯
            </button>

        </div>

    @else

        <div class="rounded-xl border border-white/10 bg-[#13131e] px-5 py-8 text-center">
            <p class="text-sm text-gray-500">
                No {{ strtolower($title) }} available right now.
            </p>
        </div>

    @endif

</section>

