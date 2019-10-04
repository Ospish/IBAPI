<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyController extends Controller
{


    public function sendNotify($request)
        //public function sendInviteLink(Request $request, $id)
    {
        //$query_email = DB::table('users')->select('email')->where('role', '=','0')->first();
        $email = 'info@inbloomshop.ru';
        $products2 = $costs2 = $colors2 = [];
        $flowers = ["Роза", "Сакура", "Гортензия", "Гербера", "Георгин", "Пион", "Хризантема", "Гвоздика"];
        $colors = ["Красный", "Оранжевый", "Желтый", "Зеленый", "Голубой", "Синий", "Фиолетовый", "Белый", "Черный"];
        if ($request->type != 1) {
            if ($request->type == 2) {
                $products = DB::table('products_stock')->select('name')->whereIn('id', json_decode($request->posInfo_products))->get();
                $costs = DB::table('products_stock')->select('price')->whereIn('id', json_decode($request->posInfo_products))->get();
            } elseif ($request->type == 0) {
                $products = DB::table('products')->select('name')->whereIn('id', json_decode($request->posInfo_products))->get();
                $costs = DB::table('products')->select('price')->whereIn('id', json_decode($request->posInfo_products))->get();
            }
            foreach ($products as $product) {
                array_push($products2, $product->name);
            }
            foreach ($costs as $cost) {
                array_push($costs2, $cost->price);
            }
        } else {
                for ($x = 0; $x < strlen($request->posInfo_flowers); $x++) {
                    array_push($products2, $flowers[$request->posInfo_flowers[$x]]);
                    foreach (json_decode($request->posInfo_colors)[$x] as $index => $color) {
                        if ($color == 1) {
                            array_push($colors2, $colors[$index]);
                        }
                    }
                    array_push($costs2, $colors2);
                    $colors2 = [];
                }
        }
        Mail::send('note', [
            'name' => $request->posInfo_name,
            'email' => $request->posInfo_email,
            'size' => $request->posInfo_size,
            'boxcolor' => $colors[$request->posInfo_boxColor],
            'quantity' => json_decode($request->posInfo_quantity),
            'productNames' => $products2,
            'productIds' => json_decode($request->posInfo_products),
            'costs' => $costs2,
            'phone' => $request->phone,
            'value' => $request->value,
            'type' => $request->type,
            'city' => $request->city,
            'geo' => $request->geo,
            'date' => $request->receiveDate,
            'congrats' => $request->congrats,
        ],
        function ($m) use ($email) {
            $m->from('info@inbloomshop.ru', 'Inbloom');
            $m->to($email, 'name')->subject('Новая заявка');
        });
        if ($request->type != 2) {
            $email = $request->posInfo_email;
            Mail::send('note', [
                'name' => $request->posInfo_name,
                'email' => $request->posInfo_email,
                'size' => $request->posInfo_size,
                'boxcolor' => $colors[$request->posInfo_boxColor],
                'quantity' => json_decode($request->posInfo_quantity),
                'productNames' => $products2,
                'productIds' => json_decode($request->posInfo_products),
                'costs' => $costs2,
                'phone' => $request->phone,
                'value' => $request->value,
                'type' => $request->type,
                'city' => $request->city,
                'geo' => $request->geo,
                'date' => $request->receiveDate,
                'congrats' => $request->congrats,
            ],
            function ($m) use ($email) {
                $m->from('info@inbloomshop.ru', 'Inbloom');
                $m->to($email, 'name')->subject('Новая заявка');
            });
        }
    }

    public function sendNotifyEmail(Request $request)
    {
        if (!isset($request->geo)) $request->geo = '';
        $this->validateEmail($request);
        $this->sendNotify($request);
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
