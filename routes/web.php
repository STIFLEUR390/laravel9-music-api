<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::mailPreview();

Route::get('/', [HomeController::class, 'index']);


Route::get('/storage/link', function(){
    foreach (array_keys(config('filesystems.links')) as $value) {
        if (file_exists($value)) {
            File::deleteDirectory($value);
        }
    }

    Artisan::call('storage:link');

    dd('lien symbolique ok');
});

Route::get('/storage/del', function(){
    foreach (array_keys(config('filesystems.links')) as $value) {
        if (file_exists($value)) {
            File::deleteDirectory($value);
        }
    }

    dd('lien symbolique delete');
});


Route::get('/migration', function(){

    Artisan::call('migrate:fresh');

    dd('migration ok');
});

Route::get('/backup', function(){

    Artisan::call('backup:run');

    dd('sauvegarde ok');
});

Route::get('/optimize', function(){

    Artisan::call('optimize:clear');

    Artisan::call('config:cache');

    Artisan::call('event:cache');

    Artisan::call('route:cache');

    Artisan::call('view:cache');

    dd('optimize ok');
});
