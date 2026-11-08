<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TMDbService
{
   protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->apiKey = config('services.tmdb.key');
    }

    /**
     * Make a cached TMDb API request.
     */
    public function get(string $endpoint, array $params = [], int $cacheMinutes = 10): array
    {
        $params['api_key'] = $this->apiKey;

        $cacheKey = 'tmdb_' . md5($endpoint . json_encode($params));

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

    public function trendingMovies(): array
    {
        return $this->get('/trending/movie/day');
    }

    public function trendingTV(): array
    {
        return $this->get('/trending/tv/week');
    }

    public function popularMovies(int $page = 1): array
    {
        return $this->get('/movie/popular', ['page' => $page]);
    }

    public function popularTV(int $page = 1): array
    {
        return $this->get('/tv/popular', ['page' => $page]);
    }

    public function latestMovies(int $page = 1): array
    {
        return $this->get('/discover/movie', [
            'page' => $page,
            'sort_by' => 'primary_release_date.desc',
            'include_adult' => false,
        ]);
    }

    public function movieDetails(int $id): array
    {
        return $this->get("/movie/{$id}", [
            'append_to_response' => 'credits,videos,similar,recommendations',
        ], 30);
    }

    public function tvDetails(int $id): array
    {
        return $this->get("/tv/{$id}", [
            'append_to_response' => 'credits,videos,similar,recommendations',
        ], 30);
    }

    public function tvSeason(int $id, int $season): array
    {
        return $this->get("/tv/{$id}/season/{$season}", [], 30);
    }

    public function search(string $query, int $page = 1): array
    {
        return $this->get('/search/multi', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ], 3);
    }

    public function category(string $type, string $slug, int $page = 1): array
    {
        $categories = [
            'movie' => [
                'trending' => '/trending/movie/week',
                'popular' => '/movie/popular',
                'action' => '/discover/movie',
                'comedy' => '/discover/movie',
                'bollywood' => '/discover/movie',
                'hollywood' => '/discover/movie',
            ],
            'tv' => [
                'trending' => '/trending/tv/week',
                'popular' => '/tv/popular',
                'korean' => '/discover/tv',
                'action' => '/discover/tv',
            ],
        ];

        if (!isset($categories[$type][$slug])) {
            return ['results' => [], 'total_pages' => 0];
        }

        $endpoint = $categories[$type][$slug];

        $filters = match ($slug) {
            'action' => ['with_genres' => '28,12'],
            'comedy' => ['with_genres' => '35'],
            'bollywood' => ['with_original_language' => 'hi'],
            'hollywood' => ['with_original_language' => 'en'],
            'korean' => ['with_original_language' => 'ko'],
            default => [],
        };

        if (str_starts_with($endpoint, '/trending/')) {
            return $this->get($endpoint, [], 10);
        }

        return $this->get($endpoint, array_merge(
            ['page' => $page],
            $filters
        ));
    }
}
