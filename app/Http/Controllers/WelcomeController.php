<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $feeds_shows = [];
        $page = 1;

        $feeds_shows = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/popular?language=en-US&page={$page}")->body();
        $feeds_shows = json_decode($feeds_shows, true);

        if (!Session::has('movie_id')) {
            $daily_movie_index = rand(0, count($feeds_shows['results']) - 1);
            $movie_id = $feeds_shows['results'][$daily_movie_index]['id'];
            Session::put('movie_id', $movie_id);
        }
        
        $movie_id = Session::get('movie_id');

        $movie = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}")->body();
        $movie = json_decode($movie, true);

        $images = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}/images")->body();
        $images = json_decode($images, true);

        $videos = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}/videos")->body();
        $videos = json_decode($videos, true);

        $video = $videos['results'][0];

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'feeds' => $feeds_shows,
            'movie' => $movie,
            'images' => $images,
            'video' => $video,
        ]);
    }
}
