<?php

namespace App\Http\Controllers;

use App\Services\TMDbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    /**
     * Movies and TV homepage.
     */
    public function index(TMDbService $tmdb)
    {
        $apiErrors = [];

        $trendingMovies = [];
        $popularMovies = [];
        $trendingTV = [];
        $popularTV = [];
        $latestMovies = [];
        $koreanDramas = [];

        $requests = [
            'trendingMovies' => fn () => $tmdb->trendingMovies(),
            'popularMovies' => fn () => $tmdb->popularMovies(),
            'trendingTV' => fn () => $tmdb->trendingTV(),
            'popularTV' => fn () => $tmdb->popularTV(),
            'latestMovies' => fn () => $tmdb->latestMovies(),
            'koreanDramas' => fn () => $tmdb->category('tv', 'korean'),
        ];

        foreach ($requests as $key => $callback) {
            try {
                $response = $callback();
                ${$key} = $response['results'] ?? [];
            } catch (\Throwable $e) {
                Log::error("TMDb {$key} error: " . $e->getMessage());
                $apiErrors[] = "Unable to load {$key}.";
            }
        }

        return view('movies.index', compact(
            'trendingMovies',
            'popularMovies',
            'trendingTV',
            'popularTV',
            'latestMovies',
            'koreanDramas',
            'apiErrors'
        ));
    }

    /**
     * Movie or TV category page.
     */
    public function category(
        string $type,
        string $slug,
        Request $request,
        TMDbService $tmdb
    ) {
        abort_unless(in_array($type, ['movie', 'tv']), 404);

        $page = max(1, (int) $request->query('page', 1));

        $response = [];
        $apiErrors = [];

        try {
            $response = $tmdb->category($type, $slug, $page);
        } catch (\Throwable $e) {
            Log::error('TMDb category error: ' . $e->getMessage());
            $apiErrors[] = 'Unable to load this category.';
        }

        $items = $response['results'] ?? [];

        return view('movies.category', [
            'items' => $items,
            'type' => $type,
            'slug' => $slug,
            'page' => $page,
            'totalPages' => $response['total_pages'] ?? 1,
            'apiErrors' => $apiErrors,
        ]);
    }


public function show(string $type, int $id, TMDbService $tmdb)
{
    abort_unless(in_array($type, ['movie', 'tv']), 404);

    try {
        $details = $type === 'movie'
            ? $tmdb->movieDetails($id)
            : $tmdb->tvDetails($id);

        // Cast members
        $cast = data_get($details, 'credits.cast', []);

        // Trailer information
        $videos = data_get($details, 'videos.results', []);

        // Recommended movies or TV series
        $recommendations = data_get(
            $details,
            'recommendations.results',
            []
        );

        // Similar titles
        $similar = data_get(
            $details,
            'similar.results',
            []
        );

        return view('movies.show', [
            'movie' => $details,
            'type' => $type,
            'cast' => $cast,
            'videos' => $videos,
            'recommendations' => $recommendations,
            'similar' => $similar,
        ]);

    } catch (\Throwable $e) {
        Log::error('TMDb details error: ' . $e->getMessage());

        abort(404, 'Movie or TV details could not be loaded.');
    }
}


    /**
     * Movie or TV watch page.
     */
    public function watch(
        string $type,
        int $id,
        TMDbService $tmdb
    ) {
        abort_unless(in_array($type, ['movie', 'tv']), 404);

        try {
            $details = $type === 'movie'
                ? $tmdb->movieDetails($id)
                : $tmdb->tvDetails($id);

            return view('movies.watch', [
                'movie' => $details,
                'type' => $type,
            ]);

        } catch (\Throwable $e) {
            Log::error('TMDb watch page error: ' . $e->getMessage());

            abort(404, 'Movie or TV details could not be loaded.');
        }
    }

    /**
     * Show TV seasons and episodes.
     */
    public function tvEpisodes(int $id, TMDbService $tmdb)
    {
        try {
            $tv = $tmdb->tvDetails($id);

            return view('movies.episodes', [
                'tv' => $tv,
                'seasons' => $tv['seasons'] ?? [],
            ]);

        } catch (\Throwable $e) {
            Log::error('TV episodes error: ' . $e->getMessage());

            abort(404, 'TV episodes could not be loaded.');
        }
    }

    /**
     * Show a selected TV season and episode list.
     */
    public function watchEpisode(
        int $id,
        int $season,
        int $episode,
        TMDbService $tmdb
    ) {
        try {
            $tv = $tmdb->tvDetails($id);
            $seasonDetails = $tmdb->tvSeason($id, $season);

            $selectedEpisode = collect(
                $seasonDetails['episodes'] ?? []
            )->firstWhere('episode_number', $episode);

            abort_if(empty($selectedEpisode), 404);

            return view('movies.episode-watch', [
                'tv' => $tv,
                'season' => $seasonDetails,
                'episode' => $selectedEpisode,
            ]);

        } catch (\Throwable $e) {
            Log::error('TV episode page error: ' . $e->getMessage());

            abort(404, 'TV episode could not be loaded.');
        }
    }
}
