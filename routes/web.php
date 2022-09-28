<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/storage/link', function(){
    foreach (array_keys(config('filesystems.links')) as $value) {
        if (file_exists($value)) {
            File::deleteDirectory($value);
        }
    }

    Artisan::call('storage:link');

    dd('lien symbolique ok');
});


Route::get('/optimize', function(){
    Artisan::call('optimize:clear');

    dd('optimize ok');
});
