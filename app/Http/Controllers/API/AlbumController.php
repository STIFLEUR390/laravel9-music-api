<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Resources\AlbumResource;
use App\Http\Controllers\BaseController;
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
        $albums = Album::with(['artist', 'music'])->latest()->get();
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
        $album->photo = $this->uploadFile($request->photo);
        $album->save();

        $this->sendResponse($album, __('The photo in the album has been updated successfully'));
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
        $album->delete();
        $this->sendResponse([], __('The album was successfully deleted'));
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
}
