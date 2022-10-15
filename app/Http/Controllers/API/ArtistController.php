<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Music;
use App\Models\Artist;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Resources\ArtistResource;
use App\Http\Controllers\BaseController;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class ArtistController extends BaseController
{
    use UploadFile;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artists = Artist::with(['albums', 'music'])->whereHas('music', function (Builder $query) {
            $query->whereNotNull('artwork');
        })->latest()->get();
        return $this->sendResponse(ArtistResource::collection($artists), '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $artist = Artist::with(['albums', 'music'])->where('id', $id)->first();
        return $this->sendResponse(new ArtistResource($artist), '');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Artist $artist)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'file', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Error validation'), $validator->errors());
        }
        $artist->photo = $this->uploadFile($request->photo, $request->photo->getClientOriginalName(), 'upload/music/artist/', $artist->photo);
        $artist->save();

        return $this->sendResponse(new ArtistResource($artist), __("The artist's photo has been successfully changed"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artist $artist)
    {
        $this->deleteFile($artist->photo);
        foreach ($artist->albums as $value) {
            $this->deleteAlbum(Album::find($value->id));
        }
        foreach ($artist->music as $value) {
            $this->musicDelete(Music::find($value->id));
        }
        $artist->delete();
        return $this->sendResponse([], __("The artist and these tracks have been successfully deleted"));
    }

    public function deleteAlbum(Album $album)
    {
        $this->deleteFile($album->photo);

        foreach ($album->music as $value) {
            $this->musicDelete(Music::find($value->id));
        }
        $album->delete();
    }

    public function musicDelete(Music $music)
    {
        $this->deleteFile($music->path);
        $this->deleteFile($music->artwork);
        $this->deleteFile($music->image);
        $music->delete();
    }

    public function getArtist(Request $request) {
        $validator = Validator::make($request->all(), [
            'paginate' => 'required',
            'search' => 'nullable',
            'page' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Error validation'), $validator->errors());
        }
        $paginate = $request->paginate;
        $search = !empty($request->search) ? $request->search : null;
        $current_page = !empty($request->page) ? $request->page : 1;

        $queryAr = Artist::with(['albums', 'music']);
        if ($search) {
            $queryAr->where('name', 'like', "%".$search."%");
        }

        /* $artists = $queryAr->whereHas('music', function (Builder $query) {
            $query->whereNotNull('artwork');
        })->latest()->paginate($paginate, ['*'], 'current_page', $current_page);
         */
        $artists = $queryAr->latest()->paginate($paginate, ['*'], 'current_page', $current_page);
        return ArtistResource::collection($artists);
    }
}
