<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyController extends Controller
{


    public function sendNotify($info_email, $info_products, $info_quantity, $phone, $value, $type, $city, $name)
        //public function sendInviteLink(Request $request, $id)
    {
        //$query_email = DB::table('users')->select('email')->where('role', '=','0')->first();
        $email = 'ospish@gmail.com';
        $products2 = $costs2 = [];
        if (($type == 0) || ($type == 1)) {
            $products = DB::table('products')->select('name')->whereIn('id', json_decode($info_products))->get();
            $costs = DB::table('products')->select('price')->whereIn('id', json_decode($info_products))->get();
        }
        if ($type == 2) {
            $products = DB::table('products_stock')->select('name')->whereIn('id', json_decode($info_products))->get();
            $costs = DB::table('products_stock')->select('price')->whereIn('id', json_decode($info_products))->get();
        }
        foreach ($products as $product) {
            array_push($products2, $product->name) ;
        }
        foreach ($costs as $cost) {
            array_push($costs2, $cost->price) ;
        }
        Mail::send('note', [
            'info_email' => $info_email,
            'info_products' => $products2,
            'info_ids' => json_decode($info_products),
            'info_quantity' => json_decode($info_quantity),
            'info_costs' => $costs2,
            'phone' => $phone,
            'value' => $value,
            'type' => $type,
            'city' => $city,
            'name' => $name
        ],
        function ($m) use ($email) {
            $m->from('info@inbloomshop.ru', 'Inbloom');
            $m->to($email, 'name')->subject('Новая заявка');
        });
    }

    public function sendNotifyEmail(Request $request)
    {
        $this->validateEmail($request);
        $this->sendNotify(
            $request->posInfo_email,
            $request->posInfo_products,
            $request->posInfo_quantity,
            $request->phone,
            $request->value,
            $request->type,
            $request->city,
            $request->name
        );
    }
    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
    }
}
?>
