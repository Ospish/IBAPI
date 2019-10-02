<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ShopRequestController extends Controller
{
    public function __construct() {
    }

    public function getAllRequests() {
        return DB::table('requests_shop')
            ->join('userinfo', 'requests_shop.userid', '=', 'userinfo.id')
            ->select('requests_shop.*', 'userinfo.name')
            ->get();
    }

    public function getRequestsByUser($id) {
        return DB::table('requests_shop')->select()->where('userid', $id)->get();
    }
    public function editRequest($id, Request $request) {
        DB::table('userinfo')->where('id', $request->userid)->update([
            'last_sale' => now(),
        ]);
        DB::table('history')->insert([
            'date' => now(),
            'userid' => $request->userid,
            'value' => $request->value,
            'action' => $request->status,
            'details' => $request->details
        ]);
        DB::table('requests_shop')->where('id', $id)->update([
            'updated_at' => now(),
            'status' => $request->status,
        ]);
        return 'Request Closed';
    }
    public function addRequest(Request $request) {
        DB::table('history')->insert([
            'date' => now(),
            //'userid' => $request->userid,
            'action' => "request_added"
        ]);
        if ($request->type == 0) {
            $product = DB::table('products')->select()->where('id', json_decode($request->posInfo_products)[0])->first();
            $request->posInfo_flowers = $product->posInfo_flowers;
            $request->value = $product->price;
            $request->posInfo_size = $product->posInfo_size;
            $request->posInfo_colors = $product->posInfo_colors;
            if ($product->posInfo_boxColor != '') $request->posInfo_boxColor = $product->posInfo_boxColor;
        }
        DB::table('requests_shop')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'userid' => $request->userid,
            'city' => $request->city,
            'receiveDate' => $request->receiveDate,
            'posInfo_name' => $request->posInfo_name,
            'posInfo_size' => $request->posInfo_size,
            'posInfo_flowers' => $request->posInfo_flowers,
            'posInfo_colors' => $request->posInfo_colors,
            'posInfo_boxColor' => $request->posInfo_boxColor,
            'posInfo_quantity' => $request->posInfo_quantity,
            'posInfo_products' => $request->posInfo_products,
            'value' => $request->value,
            'status' => 'request_added',
            'type' => $request->type,
            'phone' => $request->phone,
            'congrats' => "$request->congrats"
        ]);
        return 'Request Added';
    }
    public function deleteRequest(Request $request) {
        return DB::table('requests_shop')->where('id', $request->id)->update([
            'updated_at' => now(),
            'status' => "request_deleted"
        ]);
        //return DB::table('requests_shop')->where('id', $request->id)->delete();
    }
}
