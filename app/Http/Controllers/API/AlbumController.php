<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Music;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Resources\AlbumResource;
use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class AlbumController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::with(['artist', 'music'])->whereHas('music', function (Builder $query) {
            $query->whereNotNull('artwork');
        })->latest()->get();
        return $this->sendResponse(AlbumResource::collection($albums), '');
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
        $album = Album::with(['artist', 'music'])->where('id', $id)->first();
        return $this->sendResponse(new AlbumResource($album), '');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Album $album)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'file', 'max:10240'],
        ]);
        if ($validator->fails()) {
            return $this->sendError(__('Error validation'), $validator->errors());
        }

        $album = Album::find($album->id);
        $album->photo = $this->uploadFile($request->photo);
        $album->save();

        return $this->sendResponse(new AlbumResource($album), __('The photo in the album has been updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        if (!empty($album->photo) && File::exists($album->photo)) {
            File::delete($album->photo);
        }
        foreach ($album->music as $value) {
            $this->musicDelete(Music::find($value->id));
        }
        $album->delete();
        return $this->sendResponse([], __('The album was successfully deleted'));
    }

    public function uploadFile($file, $exitpath = null)
    {
        if (!empty($exitpath) && File::exists($exitpath)) {
            File::delete($exitpath);
        }

        $name = $file->getClientOriginalName();
        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $file->storeAs('public/upload/music/album/', $filename);
        $path = 'storage/upload/music/album/' . $filename;

        return $path;
    }

    public function musicDelete(Music $music)
    {
        if (!empty($music->path) && File::exists($music->path)) {
            File::delete($music->path);
        }
        if (!empty($music->artwork) && File::exists($music->artwork)) {
            File::delete($music->artwork);
        }
        if (!empty($music->image) && File::exists($music->image)) {
            File::delete($music->image);
        }
        $music->delete();
    }
}
