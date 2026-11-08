<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TenraiService
{
     protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.tenrai.base_url', 'https://api.tenrai.org/v1'),
            '/'
        );
    }

    /**
     * Make a cached anime API request.
     */
    public function get(
        string $endpoint,
        array $params = [],
        int $cacheMinutes = 10
    ): array {
        $cacheKey = 'tenrai_' . md5($endpoint . json_encode($params));

        return Cache::remember(
            $cacheKey,
            now()->addMinutes($cacheMinutes),
            function () use ($endpoint, $params) {
                return Http::acceptJson()
                    ->timeout(20)
                    ->retry(2, 300)
                    ->get($this->baseUrl . $endpoint, $params)
                    ->throw()
                    ->json();
            }
        );
    }

    public function popularAnime(int $page = 1): array
    {
        return $this->get('/top/anime', [
            'filter' => 'bypopularity',
            'page' => $page,
            'limit' => 24,
            'sfw-strict' => true,
        ]);
    }

    public function topAnime(int $page = 1): array
    {
        return $this->get('/top/anime', [
            'page' => $page,
            'limit' => 24,
            'sfw-strict' => true,
        ]);
    }

    public function airingAnime(int $page = 1): array
    {
        return $this->get('/top/anime', [
            'filter' => 'airing',
            'page' => $page,
            'limit' => 24,
            'sfw-strict' => true,
        ]);
    }

    public function upcomingAnime(int $page = 1): array
    {
        return $this->get('/top/anime', [
            'filter' => 'upcoming',
            'page' => $page,
            'limit' => 24,
            'sfw-strict' => true,
        ]);
    }

    public function searchAnime(string $query, int $page = 1): array
    {
        return $this->get('/anime', [
            'q' => $query,
            'page' => $page,
            'limit' => 24,
            'sfw-strict' => true,
        ], 3);
    }

    public function animeDetails(int $id): array
    {
        return $this->get("/anime/{$id}/full", [], 60);
    }

    public function genres(): array
    {
        return $this->get('/genres/anime', [], 1440);
    }

    public function animeByGenre(int $genreId, int $page = 1): array
    {
        return $this->get('/anime', [
            'genres' => $genreId,
            'page' => $page,
            'limit' => 24,
            'order_by' => 'score',
            'sort' => 'desc',
            'sfw-strict' => true,
        ]);
    }

    public function animeRecommendations(int $id): array
    {
        return $this->get("/anime/{$id}/recommendations", [], 60);
    }

    /**
 * Get episodes for an anime.
 */
public function animeEpisodes(int $id, int $page = 1): array
{
    return $this->get("/anime/{$id}/episodes", [
        'page' => $page,
    ], 60);
}
}
