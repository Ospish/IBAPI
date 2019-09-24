<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyController extends Controller
{


    public function sendNotify($info_email, $info_id, $info_quantity, $phone, $value, $type)
        //public function sendInviteLink(Request $request, $id)
    {
        $query_email = DB::table('users')->select('email')->where('role', '=','0')->get();
        $email = 'ospish@gmail.com';
        $product = DB::table('products')->select('name')->where('id', $info_id)->get();
        Mail::send('note', [
            'user' => $query_email[0]->email,
            'info_name' => $product[0]->name,
            'info_quantity' => $info_quantity,
            'info_email' => $info_email,
            'phone' => $phone,
            'value' => $value,
            'type' => $type
        ],
        function ($m) use ($email) {
            $m->from('hello@arthouseamur.ru', 'Inbloom');
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
            $request->type
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
