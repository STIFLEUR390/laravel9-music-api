<?php

namespace App\Http\Controllers\API;

use App\Models\Artist;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Resources\ArtistResource;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artists = Artist::with(['albums', 'music'])->latest()->get();
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
        $artist->photo = $this->uploadFile($request->photo);
        $artist->save();

        $this->sendResponse($artist, __("The artist's photo has been successfully changed"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artist $artist)
    {
        if (!empty($artist->photo) && File::exists($artist->photo)) {
            File::delete($artist->photo);
        }
        $artist->delete();
        $this->sendResponse([], __("The artist and these tracks have been successfully deleted"));
    }

    public function uploadFile($file, $exitpath = null)
    {
        if (!empty($exitpath) && File::exists($exitpath)) {
            File::delete($exitpath);
        }

        $name = $file->getClientOriginalName();
        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $file->storeAs('public/upload/music/artist/', $filename);
        $path = 'storage/upload/music/artist/' . $filename;

        return $path;
    }
}
