<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

trait UploadFile {

    public function uploadFile($file, $name, $path = 'upload/music/songs/',$exitpath = null)
    {
        if (!File::isDirectory(public_path($path))) {
            File::makeDirectory(public_path($path), 0777, true, true);
        }

        if (!empty($exitpath) && File::exists($exitpath)) {
            File::delete($exitpath);
        }

        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $filePath = $file->storeAs($path, $filename, ['disk' => 'public_uploads']);
        $path = $filePath;

        return $path;
    }

    public function deleteFile($path = null)
    {
        if (!empty($path) && File::exists($path)) {
            File::delete($path);
        }
    }

    /* public function uploadFile($file, $name, $path = 'upload/music/songs',$exitpath = null)
    {
        if (!empty($exitpath) && File::exists($exitpath)) {
            File::delete($exitpath);
        }

        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $filePath = $file->storeAs($path, $filename, 'public');
        $path = 'storage/' . $filePath;

        return $path;
    } */

    /* public function uploadFile($file, $name,$exitpath = null)
    {
        if (!empty($exitpath) && FacadesFile::exists($exitpath)) {
            FacadesFile::delete($exitpath);
        }

        $filename = date('YmdHis') . '-' .Str::slug($name) .'-dev-master.' . $file->extension();
        $file->storeAs('public/upload/music/songs/', $filename);
        $path = 'storage/upload/music/songs/' . $filename;

        return $path;
    } */
}
