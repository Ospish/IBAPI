<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class FileController extends Controller
{
    use UploadTrait;

    public function showOne($type, $id)
    {
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpg' )) return 'data:image/jpeg;base64,'.base64_encode(Storage::get($type.'/'.$id.'.jpg'));
        if (Storage::disk('public')->exists( $type.'/'.$id.'.JPG' )) return 'data:image/jpeg;base64,'.base64_encode(Storage::get($type.'/'.$id.'.JPG'));
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpeg' )) return 'data:image/jpeg;base64,'.base64_encode(Storage::get($type.'/'.$id.'.jpeg'));
        if (Storage::disk('public')->exists( $type.'/'.$id.'.png' )) return 'data:image/png;base64,'.base64_encode(Storage::get($type.'/'.$id.'.png'));
    }
    public function showOneBlob($type, $id)
    {
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpg' )) return Storage::get($type.'/'.$id.'.jpg');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.JPG' )) return Storage::get($type.'/'.$id.'.JPG');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpeg' )) return Storage::get($type.'/'.$id.'.jpeg');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.png' )) return Storage::get($type.'/'.$id.'.png');
    }
    public function showAll($type)
    {

        $str = '[';
        $array = Storage::files($type.'/');
        foreach ($array as $item) {
            if ($str != '[') $str .= ', ';
            $str .= '{"id": "'.explode(".",explode("/",$item)[1])[0].'", "value": "data:image/jpeg;base64,'.base64_encode(Storage::get($item)).'"}';
        }
        $str .= ']';
        return $str;
    }
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'file|required',
        ]);
/*
            if ($request->name == '') {
                $query = DB::select('SELECT id FROM products union SELECT id FROM products_stock', [1]);
                $request->name = $query;
            }
*/
            $image = $request->file('file');
            $name = $request->name;
            $folder = '/'.$request->type;
            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
            // Upload image
            return $this->uploadOne($image, $folder, 'public', $name);
    }
    public function resizeImagePost(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('image');
        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

        $destinationPath = public_path('/thumbnail');
        $img = Image::make($image->getRealPath());
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['imagename']);

        $destinationPath = public_path('/images');
        $image->move($destinationPath, $input['imagename']);

        $this->postImage->add($input);

        return back()
            ->with('success','Image Upload successful')
            ->with('imageName',$input['imagename']);
    }
}
