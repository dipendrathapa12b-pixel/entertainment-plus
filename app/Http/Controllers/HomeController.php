<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDbService;
use App\Services\TenraiService;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{

public function index(
    TMDbService $tmdb,
    TenraiService $tenrai
) {
    // Anime collections
    $trendingAnime = [];
    $popularAnime = [];
    $topAnime = [];
    $airingAnime = [];
    $upcomingAnime = [];

    // Movie and TV collections
    $trendingMovies = [];
    $latestMovies = [];
    $popularTV = [];
    $koreanDramas = [];

    $apiErrors = [];

    // Extract results from Tenrai API responses.
    $extractAnime = function ($response) {
        $items = data_get($response, 'data.items')
            ?? data_get($response, 'data.results')
            ?? data_get($response, 'data')
            ?? data_get($response, 'items')
            ?? data_get($response, 'results')
            ?? [];

        return is_array($items) ? $items : [];
    };

    // POPULAR ANIME
    try {
        $popularAnime = $extractAnime($tenrai->popularAnime());
    } catch (\Throwable $e) {
        Log::error('Popular anime error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load popular anime.';
    }

    // TRENDING ANIME
    // Temporary: use the popularity-ranked collection until
    // a separate trending endpoint is configured.
    $trendingAnime = array_slice($popularAnime, 0, 12);

    // TOP RATED ANIME
    try {
        $topAnime = $extractAnime($tenrai->topAnime());
    } catch (\Throwable $e) {
        Log::error('Top anime error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load top-rated anime.';
    }

    // CURRENTLY AIRING ANIME
    try {
        $airingAnime = $extractAnime($tenrai->airingAnime());
    } catch (\Throwable $e) {
        Log::error('Airing anime error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load currently airing anime.';
    }

    // UPCOMING ANIME
    try {
        $upcomingAnime = $extractAnime($tenrai->upcomingAnime());
    } catch (\Throwable $e) {
        Log::error('Upcoming anime error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load upcoming anime.';
    }

    // TRENDING MOVIES
    try {
        $response = $tmdb->trendingMovies();
        $trendingMovies = $response['results'] ?? [];
    } catch (\Throwable $e) {
        Log::error('Trending movies error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load trending movies.';
    }

    // LATEST MOVIES
    try {
        $response = $tmdb->latestMovies();
        $latestMovies = $response['results'] ?? [];
    } catch (\Throwable $e) {
        Log::error('Latest movies error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load latest movies.';
    }

    // POPULAR TV SHOWS
    try {
        $response = $tmdb->popularTV();
        $popularTV = $response['results'] ?? [];
    } catch (\Throwable $e) {
        Log::error('Popular TV error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load popular TV shows.';
    }

    // KOREAN DRAMAS
    try {
        $response = $tmdb->category('tv', 'korean');
        $koreanDramas = $response['results'] ?? [];
    } catch (\Throwable $e) {
        Log::error('Korean dramas error: ' . $e->getMessage());
        $apiErrors[] = 'Unable to load Korean dramas.';
    }

    return view('home', compact(
        'trendingAnime',
        'popularAnime',
        'topAnime',
        'airingAnime',
        'upcomingAnime',
        'trendingMovies',
        'latestMovies',
        'popularTV',
        'koreanDramas',
        'apiErrors'
    ));
}


    /**
     * Search movies, TV shows and anime together.
     */
    public function search(
        Request $request,
        TMDbService $tmdb,
        TenraiService $tenrai
    ) {
        $query = trim($request->query('q', ''));

        if ($query === '') {
            return view('search', [
                'query' => '',
                'moviesAndTV' => [],
                'anime' => [],
                'apiErrors' => [],
            ]);
        }

        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $moviesAndTV = [];
        $anime = [];
        $apiErrors = [];

        // Search movies and TV shows
        try {
            $response = $tmdb->search($query);

            $moviesAndTV = collect($response['results'] ?? [])
                ->filter(fn ($item) => in_array(
                    $item['media_type'] ?? '',
                    ['movie', 'tv']
                ))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::error('TMDb search error: ' . $e->getMessage());
            $apiErrors[] = 'Movie and TV search is temporarily unavailable.';
        }

        // Search anime
        try {
            $response = $tenrai->searchAnime($query);

            $anime = data_get($response, 'data.items')
                ?? data_get($response, 'data')
                ?? $response['items']
                ?? $response['results']
                ?? [];
        } catch (\Throwable $e) {
            Log::error('Tenrai search error: ' . $e->getMessage());
            $apiErrors[] = 'Anime search is temporarily unavailable.';
        }

        return view('search', [
            'query' => $query,
            'moviesAndTV' => $moviesAndTV,
            'anime' => $anime,
            'apiErrors' => $apiErrors,
        ]);
    }

    public function searchSuggestions(
    Request $request,
    TMDbService $tmdb,
    TenraiService $tenrai
) {
    $query = trim($request->query('q', ''));

    if (mb_strlen($query) < 2) {
        return response()->json([
            'anime' => [],
            'moviesAndTV' => [],
        ]);
    }

    $anime = [];
    $moviesAndTV = [];

    // Anime suggestions
    try {
        $response = $tenrai->searchAnime($query);

        $anime = data_get($response, 'data.items')
            ?? data_get($response, 'data.results')
            ?? data_get($response, 'items')
            ?? data_get($response, 'results')
            ?? [];

        if (!is_array($anime)) {
            $anime = [];
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error(
            'Anime autocomplete error: ' . $e->getMessage()
        );
    }

    // Movie and TV suggestions
    try {
        $response = $tmdb->search($query);

        $moviesAndTV = collect($response['results'] ?? [])
            ->filter(fn ($item) => in_array(
                $item['media_type'] ?? '',
                ['movie', 'tv']
            ))
            ->take(5)
            ->values()
            ->all();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error(
            'TMDb autocomplete error: ' . $e->getMessage()
        );
    }

    return response()->json([
        'anime' => array_slice($anime, 0, 7),
        'moviesAndTV' => $moviesAndTV,
    ]);
}
}
