<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class MovieController extends Controller
{
    public function __invoke($movie_id)
    {
        $movie = [];
        $images= []; 

        $movie = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}")->body();
        $movie = json_decode($movie, true);

        $images = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}/images")->body();
        $images = json_decode($images, true);

        $similar_titles = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}/recommendations")->body();
        $similar_titles = json_decode($similar_titles, true);

        $videos = Http::withToken(env('MOVIE_API'))->get("https://api.themoviedb.org/3/movie/{$movie_id}/videos")->body();
        $videos = json_decode($videos, true);

        $trailer = null;
        foreach ($videos['results'] as $video) {
            if (strpos(strtolower($video['type']), 'trailer') !== false) {
                $trailer = $video;
                break;
            }
        }

        if (!$trailer && count($videos['results']) > 0) {
            $trailer = $videos['results'][array_rand($videos['results'])];
        }

        return Inertia::render('Movie', [
            'movie' => $movie,
            'images' => $images,
            'similar_titles' => $similar_titles,
            'video' => $trailer,
        ]);
    }
}
