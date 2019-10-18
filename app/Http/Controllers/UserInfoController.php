<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserInfoController extends Controller
{
    public function __construct()
    {

    }

    public function setInfo(Request $request)
    {
        DB::update('update userinfo set 
        name = "'.$request->name.'", 
        surname = "'.$request->surname.'",
        patronymic = "'.$request->patronymic.'",  
        corp_email = "'.$request->corp_email.'",
        phone = "'.$request->phone.'",
        countrycode = "'.$request->countrycode.'",
        city = "'.$request->city.'",
        street = "'.$request->street.'",
        building = "'.$request->building.'",
        coords = "'.$request->coords.'",
        terminal = "'.$request->terminal.'",
        points = "'.$request->points.'"
        where id = '.$request->id, [1]);
    }
    public function getInfo($id)
    {
        return DB::select('select * from userinfo where id = '.$id, [1]);

           /*
            UserInfo::select([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);*/
    }

    public function setSocials(Request $request)
    {
        DB::update('update social_ids set 
        updated_at = now(),        
        vk = "'.$request->ids['vk'].'", 
        instagram = "'.$request->ids['instagram'].'",
        ok = "'.$request->ids['ok'].'",
        whatsapp = "'.$request->ids['whatsapp'].'",
        telegram = "'.$request->ids['telegram'].'",
        facebook = "'.$request->ids['facebook'].'"
        where id = '.$request->id, [1]);
        DB::update('update social_active set 
        updated_at = now(),        
        vk = '.$request->active['vk'].', 
        instagram = '.$request->active['instagram'].',
        ok = '.$request->active['ok'].',
        whatsapp = '.$request->active['whatsapp'].',
        telegram = '.$request->active['telegram'].',
        facebook = '.$request->active['facebook'].'
        where id = '.$request->id, [1]);
        return $request->id;
    }
    public function getUserByCity(Request $request)
    {
        $user = DB::select('select userinfo.id from userinfo inner join users on userinfo.id = users.id where city like "'.$request->city.'" and users.role > 0 order by last_sale limit 1', [1]);
        /*
            $user = DB::table('userinfo')
            ->join('users', 'userinfo.id', '=', 'users.id')
            ->select('userinfo.id')
            ->where(['users.role', '>', 0], ['userinfo.city', $request->city])
            ->orderBy('last_sale', 'asc')
            ->first();
        */
        return $user[0]->id;
   }
    public function getCities(Request $request)
    {
        return DB::select('select distinct userinfo.city from userinfo inner join users on userinfo.id = users.id where users.role > 0 and userinfo.city not like ""', [1]);
    }
    public function getPartnerMap(Request $request)
    {
        return DB::select('select users.role, userinfo.coords, userinfo.corp_email, userinfo.phone from userinfo inner join users on users.id = userinfo.id where users.role > 0', [1]);
    }
    /*
    public function getStats($id)
    {

        $query[0] = DB::select('select * from history where userid like ' . $id . ' and action like "request_closed" and day(date) like day(curdate())', [1]);
        $query[1] = DB::select('select * from history where userid like ' . $id . ' and action like "request_closed" and month(date) like month(curdate())', [1]);
        $query[2] = DB::select('select * from history where userid like ' . $id . ' and action like "request_closed" and year(date) like year(curdate())', [1]);
        foreach ($query as $key => $value) {
            $count = $sum = $midval = 0;
            $result = $values = [];
            foreach ($value as $item) {
                $result->$values[$count++] = $item->value;
                $sum += $item->value;
            }
            if ($count > 0) {$midval = $sum / $count;}
            $result[$key] = [$values, $count, $midval, $sum];
         }
        return $result;
    }
    */
    public function getStats($id)
    {
        $result = $values = $time = [];
        $count = $sum = $midval = 0;
        $query = DB::select('select * from requests_shop where userid like ' . $id . ' and status like "request_closed" and type not like 2 and day(updated_at) like day(curdate())', [1]);
        foreach ($query as $item) {
            $values[$count] = $item->value;
            $time[$count] = date('g', strtotime($item->updated_at));
            $sum += $item->value;
            $count++;
        }
        if ($count > 0) {$midval = $sum / $count;}
        $result[0] = [$values, $count, $midval, $sum, $time];

        $values = $days = [];
        $count = $sum = $midval = 0;
        $query = DB::select('select * from requests_shop where userid like ' . $id . ' and status like "request_closed" and type not like 2 and month(updated_at) like month(curdate())', [1]);
        foreach ($query as $item) {
            $values[$count] = $item->value;
            $days[$count] = date('j', strtotime($item->updated_at));
            $sum += $item->value;
            $count++;
        }
        if ($count > 0) {$midval = $sum / $count;}
        $result[1] = [$values, $count, $midval, $sum, $days];

        $values = $months = [];
        $count = $sum = $midval = 0;
        $query = DB::select('select * from requests_shop where userid like ' . $id . ' and status like "request_closed" and type not like 2 and year(updated_at) like year(curdate())', [1]);
        foreach ($query as $item) {
            $values[$count] = $item->value;
            $months[$count] = date('M', strtotime($item->updated_at));
            $sum += $item->value;
            $count++;
        }
        if ($count > 0) {$midval = $sum / $count;}
        $result[2] = [$values, $count, $midval, $sum, $months];

        return $result;
    }

    public function getSocials($id)
    {
        $active = DB::select('select vk, ok, facebook, instagram, whatsapp, telegram from social_ids where id = '.$id, [1]);
        $url = DB::select('select vk, ok, facebook, instagram, whatsapp, telegram from social_active where id = '.$id, [1]);
        return [ $active, $url ];
    }
    public function getAllSocials($id)
    {
        $active = DB::select('select * from social_ids', [1]);
        $url = DB::select('select * from social_active where id = '.$id, [1]);
        return [ $active, $url ];
    }
    public function getHistory($id)
    {
        return DB::select('select * from requests_shop where userid like '.$id.' and status not like "request_added" order by requests_shop.id asc', [1]);
    }
    public function getPartners(Request $request)
    {
        $users = DB::select('
            SELECT users.id, users.role, users.email, userinfo.imgext, userinfo.name, userinfo.city, social_ids.vk, social_ids.instagram, social_ids.telegram, social_ids.whatsapp FROM users 
            LEFT OUTER JOIN userinfo ON users.id = userinfo.id
            LEFT OUTER JOIN social_ids ON users.id = social_ids.id
            WHERE users.role > 1;
            ', [1]);
        return $users;
    }
    public function setLastSale(Request $request, $id)
    {
        return DB::update('update userinfo set last_sale = now() where id = '.$id, [1]);
    }
    /*
    public function setPartner(Request $request)
    {
        $result = DB::update('update * from products where id = '.$request->$id, [1]);
        return response()->json($result, 200);
    }
    */
}
