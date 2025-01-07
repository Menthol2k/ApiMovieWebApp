<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class MovieController extends Controller
{
    public function __invoke($movie_id)
    {
        return Inertia::render('Movie', [
            'movie_id' => $movie_id
        ]);
    }
}
