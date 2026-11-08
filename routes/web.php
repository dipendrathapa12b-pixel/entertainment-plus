
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AnimeController;

// Combined homepage
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Movies and TV
Route::get('/movies', [MovieController::class, 'index'])
    ->name('movies.index');

Route::get('/movies/category/{type}/{slug}', [MovieController::class, 'category'])
    ->name('movies.category');

Route::get('/movies/{type}/{id}', [MovieController::class, 'show'])
    ->where(['type' => 'movie|tv', 'id' => '[0-9]+'])
    ->name('movies.show');

Route::get('/watch/{type}/{id}', [MovieController::class, 'watch'])
    ->where(['type' => 'movie|tv', 'id' => '[0-9]+'])
    ->name('movies.watch');

Route::get('/tv/{id}/episodes', [MovieController::class, 'tvEpisodes'])
    ->whereNumber('id')
    ->name('movies.tv.episodes');

Route::get('/watch/tv/{id}/{season}/{episode}', [MovieController::class, 'watchEpisode'])
    ->whereNumber('id')
    ->whereNumber('season')
    ->whereNumber('episode')
    ->name('movies.tv.episode');

// Anime
Route::get('/anime', [AnimeController::class, 'index'])
    ->name('anime.index');

Route::get('/anime/search', [AnimeController::class, 'searchSuggestions'])
    ->name('anime.search');

Route::get('/anime/category/{category}', [AnimeController::class, 'category'])
    ->name('anime.category');

Route::get('/anime/{id}', [AnimeController::class, 'show'])
    ->whereNumber('id')
    ->name('anime.show');

// Anime genres
Route::get('/genres', [AnimeController::class, 'genres'])
    ->name('genres.index');

Route::get('/genres/{id}', [AnimeController::class, 'genre'])
    ->whereNumber('id')
    ->name('genres.show');

// Anime player
Route::get('/watch/anime/{id}', [AnimeController::class, 'watch'])
    ->whereNumber('id')
    ->name('watch.anime');

// Combined search
Route::get('/search', [HomeController::class, 'search'])
    ->name('search');

    Route::get('/search/suggestions', [HomeController::class, 'searchSuggestions'])
    ->name('search.suggestions');
