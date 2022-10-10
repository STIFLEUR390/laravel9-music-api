<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Music;
use App\Models\Artist;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Owenoj\LaravelGetId3\GetId3;
use Illuminate\Validation\Rules\File;
use App\Http\Resources\{MusicResource};
use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File as FacadesFile;

class MusicController extends BaseController
{
    public $errorsFile = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return $this->sendResponse(MusicResource::collection(Music::orderBy('title')->get()), '');
        return $this->sendResponse(MusicResource::collection(Music::latest()->orderBy('track_number')->get()), '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'songs' => 'required',
            'songs.*' => ['required', File::types(['audio/mp3', 'audio/flac', 'audio/mpeg'])->min(1024)->max(50 * 1024),]
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Error validation'), $validator->errors());
        }

        $taille_songs = 0;
        $songs_non_enregistrer = [
            'int' => 0,
            'filles' => []
        ];
        foreach ($request->file('songs') as $file) {
            $taille_songs++;
            $track = new GetId3($file);
            if (!empty($track->getArtist())) {
                $name_artist = $this->getNameArtist($track->getArtist());
                $count = Artist::where('name', $name_artist)->get()->count();
                if (!$count) {
                    $artist = new Artist();
                    $artist->name = $name_artist;
                    $artist->slug = Str::slug($name_artist);
                    $artist->save();
                }
            }
            if (!empty($track->getAlbum())) {
                $name_album = $track->getAlbum();
                $count = Album::where('name', $name_album)->get()->count();
                if (!$count) {
                    $album = new Album();
                    $album->name = $name_album;
                    $album->slug = Str::slug($name_album);
                    if (isset($name_artist)) {
                        $artist = Artist::where("name", $name_artist)->first()->id;
                        $album->artist()->associate($artist);
                    }
                    $album->save();
                }
            }

            if (count($track->getGenres())) {
                $genreList = [];
                foreach ($track->getGenres() as $value) {
                    $genreList[] = $value;
                    $count = Genre::where('name', $value)->get()->count();
                    if (!$count) {
                        $genre = new Genre();
                        $genre->name = $value;
                        $genre->slug = Str::slug($value);
                        $genre->save();
                    }
                }
            }

            if ($track->getTitle() && $track->getArtwork() && $track->getArtist()) {
                $count = Music::whereHas('artist', function (Builder $query) use ($name_artist) {
                    $query->where('name', $name_artist);
                })->where('title', $track->getTitle())->get()->count();
                if (!$count) {
                    $artwork = $track->getArtwork(true);
                    $music_image = $this->uploadFile($artwork, $track->getTitle());

                    $music = new Music();
                    $music->title = $track->getTitle();
                    $music->playtime = $track->getPlaytime();
                    $music->playtime_s = $track->getPlaytimeSeconds();
                    $music->artwork = $music_image;
                    $music->composers = $track->getComposer();
                    $music->track_number = $track->getTrackNumber();
                    $music->copyright = $track->getCopyrightInfo();
                    $music->format = $track->getFileFormat();
                    // $music->image = $music_image;
                    $music->path = $this->uploadFile($file, $track->getTitle());

                    if (isset($name_artist)) {
                        $artist = Artist::where("name", $name_artist)->first()->id;
                        if (empty(Artist::where("name", $name_artist)->first()->photo)) {
                            $art = Artist::where("name", $name_artist)->first();
                            $art->photo = $music_image;
                            $art->save();
                        }
                        $music->artist()->associate($artist);
                    }
                    if (isset($name_album)) {
                        $album = Album::where("name", $name_album)->first()->id;
                        if (empty(Album::where("name", $name_album)->first()->photo)) {
                            $al = Album::where("name", $name_album)->first();
                            $al->photo = $music_image;
                            $al->save();
                        }
                        $music->album()->associate($album);
                    }

                    $music->save();
                    if (count($track->getGenres())) {
                        $genre_id = Genre::whereIn("name", $genreList)->pluck('id');
                        $music->genres()->attach($genre_id);//sync
                    }
                } else {
                    $songs_non_enregistrer['int'] = $songs_non_enregistrer['int'] + 1;
                    $songs_non_enregistrer['filles'][] = $file->getClientOriginalName();
                }
            } else {
                $this->errorsFile[] = $file->getClientOriginalName();
            }

        }

        if (count($this->errorsFile)) {
            return $this->sendError(__('Some audio files were not imported'), $this->errorsFile);
        } elseif ($songs_non_enregistrer['int'] == $taille_songs) {
            return $this->sendError(__('No file was imported'), $songs_non_enregistrer['filles']);
        }

        return $this->sendResponse([], __('All audio files have been successfully imported'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Music  $music
     * @return \Illuminate\Http\Response
     */
    public function show(Music $music)
    {
        return $this->sendResponse(new MusicResource($music), '');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Music  $music
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Music $music)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Music  $music
     * @return \Illuminate\Http\Response
     */
    public function destroy(Music $music)
    {
        if (!empty($music->path) && FacadesFile::exists($music->path)) {
            FacadesFile::delete($music->path);
        }
        if (!empty($music->artwork) && FacadesFile::exists($music->artwork)) {
            FacadesFile::delete($music->artwork);
        }
        if (!empty($music->image) && FacadesFile::exists($music->image)) {
            FacadesFile::delete($music->image);
        }
        $music->delete();

        return $this->sendResponse([], __('The audio file was successfully deleted'));
    }

    public function uploadFile($file, $name,$exitpath = null)
    {
        if (!empty($exitpath) && FacadesFile::exists($exitpath)) {
            FacadesFile::delete($exitpath);
        }

        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $file->storeAs('public/upload/music/songs/', $filename);
        $path = 'storage/upload/music/songs/' . $filename;

        return $path;
    }

    public function getNameArtist(String $artist)
    {
        if (strpos($artist, 'feat.') !== false) {
            $ex = explode('feat.', $artist);
            return trim($ex["0"]);
        } elseif (strpos($artist, 'Feat.') !== false) {
            $ex = explode('Feat.', $artist);
            return trim($ex["0"]);
        } elseif (strpos($artist, 'ft.') !== false) {
            $ex = explode('ft.', $artist);
            return trim($ex["0"]);
        } elseif (strpos($artist, 'Ft.') !== false) {
            $ex = explode('Ft.', $artist);
            return trim($ex["0"]);
        } elseif (strpos($artist, '/') !== false) {
            $ex = explode('/', $artist);
            return trim($ex["0"]);
        } elseif (strpos($artist, ';') !== false) {
            $ex = explode(';', $artist);
            return trim($ex["0"]);
        }

        return $artist;
    }
}
