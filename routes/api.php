<?php

use App\Http\Controllers\API\{AlbumController, ArtistController, HomeController, MusicController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('dashbord', HomeController::class);

Route::apiResource("music", MusicController::class)->except('update');
Route::post('music/paginate', [MusicController::class, 'getMusic']);

Route::apiResource("album", AlbumController::class)->except('store');
Route::post('album/paginate', [AlbumController::class, 'getAlbum']);

Route::apiResource("artist", ArtistController::class)->except('store');
Route::post('artist/paginate', [ArtistController::class, 'getArtist']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
