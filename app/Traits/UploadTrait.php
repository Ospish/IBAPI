<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : str_random(25);
        $ext = $uploadedFile->getClientOriginalExtension();
        Storage::delete($folder.'/'.$name.'.jpg');
        Storage::delete($folder.'/'.$name.'.png');
        $file = $uploadedFile->storeAs($folder, $name.'.'.$ext, $disk);

        return $file;
    }
}
