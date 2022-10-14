<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
// use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Music;

class HomeController extends BaseController
{
    public function __invoke()
    {
        $albums_count = Album::count();
        $artists_count = Artist::count();
        $genres_count = Genre::count();
        $songs_count = Music::count();

        return $this->sendResponse(compact('albums_count', 'artists_count', 'genres_count', 'songs_count'), '');
    }
}
