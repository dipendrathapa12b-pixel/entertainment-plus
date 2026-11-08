<?php

namespace App\Http\Controllers;

use App\Services\TenraiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnimeController extends Controller
{
    /**
     * Anime homepage.
     */
    public function index(TenraiService $tenrai)
    {
        $apiErrors = [];

        $popularAnime = [];
        $topAnime = [];
        $airingAnime = [];
        $upcomingAnime = [];

        try {
            $popularAnime = $this->extractItems(
                $tenrai->popularAnime()
            );
        } catch (\Throwable $e) {
            Log::error('Popular anime error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load popular anime.';
        }

        try {
            $topAnime = $this->extractItems(
                $tenrai->topAnime()
            );
        } catch (\Throwable $e) {
            Log::error('Top anime error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load top-rated anime.';
        }

        try {
            $airingAnime = $this->extractItems(
                $tenrai->airingAnime()
            );
        } catch (\Throwable $e) {
            Log::error('Airing anime error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load currently airing anime.';
        }

        try {
            $upcomingAnime = $this->extractItems(
                $tenrai->upcomingAnime()
            );
        } catch (\Throwable $e) {
            Log::error('Upcoming anime error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load upcoming anime.';
        }

        return view('anime.index', compact(
            'popularAnime',
            'topAnime',
            'airingAnime',
            'upcomingAnime',
            'apiErrors'
        ));
    }

    /**
     * Anime category pages.
     */

    public function category(
        string $category,
        Request $request,
        TenraiService $tenrai
    ) {
        $page = max(1, (int) $request->query('page', 1));

        $category = strtolower($category);

        $categories = [
            'popular' => [
                'title' => 'Popular Anime',
                'method' => 'popularAnime',
            ],
            'top' => [
                'title' => 'Top Rated Anime',
                'method' => 'topAnime',
            ],
            'airing' => [
                'title' => 'Currently Airing Anime',
                'method' => 'airingAnime',
            ],
            'upcoming' => [
                'title' => 'Upcoming Anime',
                'method' => 'upcomingAnime',
            ],
        ];

        abort_unless(isset($categories[$category]), 404);

        $items = [];
        $apiErrors = [];
        $totalPages = 1;

        try {
            $method = $categories[$category]['method'];

            $response = $tenrai->$method($page);

            $items = $this->extractItems($response);

            // Get pagination information from API
            $totalPages = (int) (
                data_get($response, 'pagination.last_visible_page')
                ?? data_get($response, 'pagination.last_page')
                ?? data_get($response, 'pagination.total_pages')
                ?? data_get($response, 'meta.last_page')
                ?? 1
            );
        } catch (\Throwable $e) {
            Log::error('Anime category error: ' . $e->getMessage());

            $apiErrors[] = 'Unable to load this anime category.';
        }

        return view('anime.category', [
            'items' => $items,
            'category' => $category,
            'title' => $categories[$category]['title'],
            'page' => $page,
            'totalPages' => max(1, $totalPages),
            'apiErrors' => $apiErrors,
        ]);
    }

    /**
     * Anime details page.
     */
    public function show(int $id, Request $request, TenraiService $tenrai)
    {
        try {
            // Fetch anime details.
            $response = $tenrai->animeDetails($id);

            $anime = $this->extractAnime($response);

            abort_if(empty($anime), 404);

            // Fetch anime recommendations.
            $recommendations = [];

            try {
                $recommendations = $this->extractItems(
                    $tenrai->animeRecommendations($id)
                );
            } catch (\Throwable $e) {
                Log::warning('Anime recommendations error: ' . $e->getMessage());
            }

            // Fetch anime episodes with pagination.
            $episodePage = max(1, (int) $request->query('episode_page', 1));

            $episodeList = [];
            $episodePagination = [];

            try {
                $episodeResponse = $tenrai->animeEpisodes($id, $episodePage);

                $episodeList = $this->extractItems($episodeResponse);

                $episodePagination = $episodeResponse['pagination'] ?? [];
            } catch (\Throwable $e) {
                Log::warning('Anime episodes error: ' . $e->getMessage());
            }

            return view('anime.show', compact(
                'anime',
                'recommendations',
                'episodeList',
                'episodePagination',
                'episodePage'
            ));
        } catch (\Throwable $e) {
            Log::error('Anime details error: ' . $e->getMessage());

            abort(404, 'Anime details could not be loaded.');
        }
    }



    /**
     * Anime genre list.
     */
    public function genres(TenraiService $tenrai)
    {
        $genres = [];
        $apiErrors = [];

        try {
            $response = $tenrai->genres();

            $genres = $this->extractItems($response);
        } catch (\Throwable $e) {
            Log::error('Anime genres error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load anime genres.';
        }

        return view('genres.index', compact(
            'genres',
            'apiErrors'
        ));
    }




    /**
     * Anime by genre.
     */

    /**
     * Display anime belonging to a specific genre.
     */
    public function genre(
        int $id,
        Request $request,
        TenraiService $tenrai
    ) {
        $page = max(1, (int) $request->query('page', 1));

        $items = [];
        $apiErrors = [];
        $genreName = 'Anime Genre';
        $totalPages = 1;

        try {
            // Fetch anime for this genre.
            $response = $tenrai->animeByGenre($id, $page);

            $items = $this->extractItems($response);

            // Read pagination information from the API response.
            $totalPages = (int) (
                data_get($response, 'pagination.last_visible_page')
                ?? data_get($response, 'pagination.last_page')
                ?? data_get($response, 'meta.last_page')
                ?? data_get($response, 'data.pagination.last_visible_page')
                ?? 1
            );

            $totalPages = max(1, $totalPages);

            // Find the genre name.
            $genreResponse = $tenrai->genres();
            $genres = $this->extractItems($genreResponse);

            foreach ($genres as $genre) {
                $genreId = data_get($genre, 'mal_id')
                    ?? data_get($genre, 'id')
                    ?? data_get($genre, 'genre_id');

                if ((int) $genreId === $id) {
                    $genreName = data_get($genre, 'name')
                        ?? data_get($genre, 'title')
                        ?? 'Anime Genre';

                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Anime genre results error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load anime for this genre.';
        }

        return view('genres.show', compact(
            'id',
            'items',
            'genreName',
            'page',
            'totalPages',
            'apiErrors'
        ));
    }


    /**
     * Anime search suggestions.
     */
    public function searchSuggestions(
        Request $request,
        TenraiService $tenrai
    ) {
        $query = trim($request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $response = $tenrai->searchAnime($query);

            return response()->json(
                $this->extractItems($response)
            );
        } catch (\Throwable $e) {
            Log::error('Anime suggestions error: ' . $e->getMessage());

            return response()->json([], 503);
        }
    }



    /**
     * Anime watch page using AnimeEmbed.
     */
    public function watch(
        int $id,
        Request $request,
        TenraiService $tenrai
    ) {
        try {
            // Fetch anime details.
            $response = $tenrai->animeDetails($id);

            $anime = $this->extractAnime($response);

            abort_if(empty($anime), 404);

            // Fetch episode information.
            $episodeList = [];
            $episodePagination = [];

            $episodePage = max(
                1,
                (int) $request->query('episode_page', 1)
            );

            try {
                $episodeResponse = $tenrai->animeEpisodes(
                    $id,
                    $episodePage
                );

                $episodeList = $this->extractItems($episodeResponse);

                $episodePagination = $episodeResponse['pagination'] ?? [];
            } catch (\Throwable $e) {
                Log::warning('Anime watch episodes error: ' . $e->getMessage());
            }

            // Select requested episode.
            $requestedEpisodeId = (int) $request->query('episode', 0);

            $selectedEpisode = null;

            if ($requestedEpisodeId > 0) {
                foreach ($episodeList as $episode) {
                    if ((int) ($episode['mal_id'] ?? 0) === $requestedEpisodeId) {
                        $selectedEpisode = $episode;
                        break;
                    }
                }
            } else {
                $selectedEpisode = $episodeList[0] ?? null;
            }

            // AnimeEmbed uses episode numbers, not MAL episode IDs.
            $episodeNumber = null;

            if ($selectedEpisode) {
                $episodeNumber = (int) (
                    $selectedEpisode['episode'] ?? 0
                );

                // Fall back to the correct position using pagination.
                if ($episodeNumber < 1) {
                    $episodeIndex = array_search(
                        $selectedEpisode,
                        $episodeList,
                        true
                    );

                    if ($episodeIndex !== false) {
                        $itemsPerPage = (int) (
                            $episodePagination['items_per_page']
                            ?? $episodePagination['per_page']
                            ?? count($episodeList)
                        );

                        $currentPage = (int) (
                            $episodePagination['current_page']
                            ?? $episodePage
                        );

                        $episodeNumber = (
                            ($currentPage - 1) * $itemsPerPage
                        ) + $episodeIndex + 1;
                    } else {
                        $episodeNumber = null;
                    }
                }
            }

            // AnimeEmbed MAL URL.
            $embedUrl = null;

            if ($episodeNumber) {
                $embedUrl = "https://animeembed.com/embed/mal/{$id}/{$episodeNumber}";
            }

            // Find previous and next episodes on the current API page.
            $selectedIndex = false;

            if ($selectedEpisode) {
                $selectedIndex = array_search(
                    $selectedEpisode,
                    $episodeList,
                    true
                );
            }

            $previousEpisode = (
                $selectedIndex !== false && $selectedIndex > 0
            ) ? $episodeList[$selectedIndex - 1] : null;

            $nextEpisode = (
                $selectedIndex !== false &&
                $selectedIndex < count($episodeList) - 1
            ) ? $episodeList[$selectedIndex + 1] : null;

            return view('watch.anime', compact(
                'anime',
                'episodeList',
                'episodePagination',
                'selectedEpisode',
                'episodeNumber',
                'embedUrl',
                'episodePage',
                'previousEpisode',
                'nextEpisode'
            ));
        } catch (\Throwable $e) {
            Log::error('Anime watch page error: ' . $e->getMessage());

            abort(404, 'Anime could not be loaded.');
        }
    }



    /**
     * Extract anime lists from different API response formats.
     */
    private function extractItems(array $response): array
    {
        $items =
            data_get($response, 'data.items')
            ?? data_get($response, 'data.results')
            ?? data_get($response, 'items')
            ?? data_get($response, 'results')
            ?? null;

        if ($items === null) {
            $data = $response['data'] ?? [];

            if (is_array($data) && array_is_list($data)) {
                $items = $data;
            }
        }

        return is_array($items)
            ? array_values(array_filter($items, 'is_array'))
            : [];
    }

    /**
     * Extract one anime record from an API response.
     */
    private function extractAnime(array $response): array
    {
        $anime =
            data_get($response, 'data.item')
            ?? data_get($response, 'data.anime')
            ?? data_get($response, 'data')
            ?? data_get($response, 'item')
            ?? data_get($response, 'anime')
            ?? $response;

        if (is_array($anime) && array_is_list($anime)) {
            $anime = $anime[0] ?? [];
        }

        return is_array($anime) ? $anime : [];
    }
}
