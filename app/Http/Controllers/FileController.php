<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;


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
        Artisan::call('storage:link', [] );
        $str = '[';
        $array = Storage::files($type.'/');
        foreach ($array as $item) {
            if ($str != '[') $str .= ', ';
            $str .= '{"id": "'.explode(".",explode("/",$item)[1])[0].'", "value": "data:image/jpeg;base64,'.base64_encode(Storage::get($item)).'"}';
        }
        $str .= ']';
        return $str;
    }

    public function showPartnerPhotos($type, $id)
    {
        $query = DB::table('products')
            ->select('id')->where('userid', $id)
            ->get();
        $str = '[';
        foreach ($query as $item) {
            if ($str != '[') $str .= ', ';
            $str .= '{"id": "'.$item->id.'", "value": "data:image/jpeg;base64,'.base64_encode($this->showOneBlob($type, $item->id)).'"}';
        }
        $str .= ']';
        return $str;
    }

    public function showPartnersByRole()
    {
        $query = DB::table('users')
            ->select('id')->where('role', '>', 1)
            ->get();
        $str = '[';
        foreach ($query as $item) {
            if ($str != '[') $str .= ', ';
            $str .= '{"id": "'.$item->id.'", "value": "data:image/jpeg;base64,'.base64_encode($this->showOneBlob('profile', $item->id)).'"}';
        }
        $str .= ']';
        return $str;
    }

    public function showProductsByUser($type, $id)
    {
        $query = DB::table('products')
            ->select('id')->where('userid', $id)
            ->get();
        $str = '[';
        foreach ($query as $item) {
            if ($str != '[') $str .= ', ';
            $str .= '{"id": "'.$item->id.'", "value": "data:image/jpeg;base64,'.base64_encode($this->showOneBlob($type, $item->id)).'"}';
        }
        $str .= ']';
        return $str;
    }

    public static function delete($type, $id)
    {
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpg' )) return Storage::delete($type.'/'.$id.'.jpg');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.JPG' )) return Storage::delete($type.'/'.$id.'.JPG');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.jpeg' )) return Storage::delete($type.'/'.$id.'.jpeg');
        if (Storage::disk('public')->exists( $type.'/'.$id.'.png' )) return Storage::delete($type.'/'.$id.'.png');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'file|required',
        ]);
        if ($request->type == 'profile') $table = 'userinfo';
        if ($request->type == 'content') $table = 'content';
        $image = $request->file('file');
        if (isset($table)) {
            DB::table($table)->where('id', $request->name)->update([
                'imgext' => $image->getClientOriginalExtension(),
            ]);
        }

        $name = $request->name;
        $folder = '/'.$request->type;
        $filePath = $folder . '/' . $name. '.' . $image->getClientOriginalExtension();
        // Upload image
        $this->uploadOne($image, $folder, 'public', $name);
        define('WEBSERVICE', 'http://api.resmush.it/ws.php?img=');
        $s = 'https://api.inbloomshop.ru/public/storage'.$filePath;
        $o = json_decode(file_get_contents(WEBSERVICE . $s));

        if(isset($o->error)){
            die('Error');
        }
        $file = file_get_contents($o->dest);
        Storage::disk('public')->put($folder . '/' . $name. '.' . $image->getClientOriginalExtension(), $file);
    }

/*
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'file|required',
        ]);
/*
            if ($request->name == '') {
                $query = DB::update('update id FROM products union SELECT id FROM products_stock', [1]);
                $request->name = $query;
            }

if ($request->type == 'profile') $table = 'userinfo';
if ($request->type == 'content') $table = 'content';
$image = $request->file('file');
DB::table($table)->where('id', $request->name)->update([
'imgext' => $image->getClientOriginalExtension(),
]);
$name = $request->name;
$folder = '/'.$request->type;
$filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
    // Upload image
return $this->uploadOne($image, $folder, 'public', $name);
}

    public function reSmush2($file)
    {

        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        $data = array(
            "files" => $output,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=80');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);
        }
        curl_close ($ch);

        return($result);
    }
*/
}
