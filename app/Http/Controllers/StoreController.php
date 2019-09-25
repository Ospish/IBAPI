<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{

    public function __construct()
    {

    }

    // PRODUCTS

    public function addProduct(Request $request, $type)
    {
        if ($type == 1) {
            DB::insert('insert into products (id, created_at, updated_at, name, posInfo_size, posInfo_flowers, posInfo_colors, posInfo_boxColor, description, price, userid)
        values (
                '.$request->id.',
                now(), 
                now(), 
                "'.$request->name.'", 
                "'.$request->posInfo_size.'",
                "'.$request->posInfo_flowers.'",
                "'.$request->posInfo_colors.'",
                "'.$request->posInfo_boxColor.'",
                "'.$request->description.'", 
                '.$request->price.',
                '.$request->userid.'
                )', [1]);
        }
        else {
            if ($request->price_premium == '') $request->price_premium = $request->price;
            if ($request->price_vip == '') $request->price_vip = $request->price;
            DB::insert('insert into products_stock (created_at, updated_at, name, description, type, sub, price, price_premium, price_vip)
            values (
                now(), 
                now(), 
                "'.$request->name.'", 
                "'.$request->description.'", 
                '.$request->type.',
                '.$request->sub.',
                '.$request->price.', 
                '.$request->price_premium.',
                '.$request->price_vip.'
                )', [1]);
        }
    }
    public function setProduct(Request $request, $type)
    {
        if ($type == 1) {
            DB::update('update products set
            updated_at = now(), 
            name = "' . $request->name . '",
            posInfo_size = "' . $request->posInfo_size . '",
            posInfo_flowers = "' . $request->posInfo_flowers . '",
            posInfo_colors = "' . $request->posInfo_colors . '",
            posInfo_boxColor = "' . $request->posInfo_boxColor . '",
            description = "' . $request->description . '",
            price = ' . $request->price . '
            where id = ' . $request->id, [1]);
            return $request->id;
        }
        else {
            DB::update('update products_stock set
            updated_at = now(), 
            name = "'.$request->name.'",
            description = "'.$request->description.'",
            price = '.$request->price.', 
            price_premium = '.$request->price_premium.',
            price_vip = '.$request->price_vip.'
            where id = '.$request->id, [1]);
        }
        return $request->id;
    }

    public function getProductsById(Request $request, $id)
    {
        $products[0] = DB::table('products_stock')
            ->select('id', 'name', 'description', 'type', 'sub', 'price', 'price_premium', 'price_vip', $id)
            ->get();
        //$products[0] = DB::select('select id, name, desc, `'.$id.'` from products_stock', [1]);
        $products[1] = DB::select('select * from products where userid like '.$id, [1]);
        return $products;
    }

    public function getAllProducts(Request $request, $id)
    {
        $products[0] = DB::table('products_stock')
            ->select('id', 'name', 'description', 'type', 'sub', 'price', 'price_premium', 'price_vip', $id)
            ->get();
        //$products[0] = DB::select('select id, name, desc, `'.$id.'` from products_stock', [1]);
        $products[1] = DB::select('select * from products', [1]);
        return $products;
    }



    public function getProduct($id)
    {
        return DB::select('select * from products where id = '.$id, [1]);
    }

    public function deleteProduct($type, $id)
    {
        DB::delete('delete from products_stock where id = '.$id, [1]);
        DB::delete('delete from products where id = '.$id, [1]);
        FileController::delete($type, $id);
    }
    public function getSiteStore($id)
    {
       return DB::select('SELECT * FROM products where userid = '.$id, [1]);
    }
    public function getStoreByCity($city)
    {
        $count = 0;
        $result = [];
        $ids = DB::select('select id from userinfo where city like "'.$city.'"', [1]);
        foreach ($ids as $x => $id) {
            $result[$x] = DB::select('SELECT products.id, products.name, products.posInfo_size, products.posInfo_colors, products.posInfo_boxColor, products.posInfo_flowers, products.price FROM products WHERE products.type = 1', [1]);
            $count++;
        }
        $finres = call_user_func_array('array_merge', $result);
        return $finres;
    }


    public function getCategories(Request $request)
    {
        $result = [];
        $cat_arr = DB::select('select name from categories', [1]);
        // Looping through all categories to add subcategories
        foreach ($cat_arr as $catid => $catname) {
            $start = array('id' => $catid, 'name' => $catname->name, 'subs' => []);
            $subcat_arr = DB::select('SELECT name FROM subcategories WHERE parent = '.$catid, [1]);
            // Adding subcategories
            foreach ($subcat_arr as $subcatid => $subcatname) {
                array_push($start['subs'], array( 'id' => $subcatid, 'name' => $subcatname->name));
            }
            array_push($result, $start);
        }
        return $result;
    }

    public function addCategory(Request $request)
    {
        // Adding category
        $query = DB::insert('INSERT INTO categories (name) VALUES ("'.$request->name.'")', [1]);
        return '$query';
    }

    public function addSubCategory(Request $request, $id)
    {
        // Adding subcategory, parent id sent in url
        $query = DB::insert('INSERT INTO subcategories (name, parent) VALUES ("'.$request->name.'", '.$id.')', [1]);
        return '$query';
    }

    // STOCKS


    public function pullStocks(Request $request)
    {
        DB::insert('insert into history (date, action, details, userid) values (now(), "stocks_wroughtoff", "'.$request->quantity.'", "'.$request->userid.'")', [1]);
        DB::update('update products_stock set 
            updated_at = now(), 
            `'.$request->userid.'` = (`'.$request->userid.'` - '.$request->quantity.')
            where id = '.$request->id, [1]);
        return $request->id;
    }

    public function pushStocks(Request $request)
    {
        DB::insert('insert into history (date, action, value, details, userid) values (now(), "request_arrived", '.$request->value.',  "'.$request->details.'", "'.$request->userid.'")', [1]);
        DB::update('update products_stock set 
            updated_at = now(), 
            `'.$request->userid.'` = (`'.$request->userid.'` + '.$request->quantity.')
            where id = '.$request->id, [1]);
        return $request->id;
    }
    // CONTENT

    public function addContent(Request $request)
    {
        DB::insert('insert into content (created_at, updated_at, description )
        values (now(), now(), "'.$request->description.'")', [1]);
    }
    public function deleteContent($id)
    {
        return DB::delete('delete from content where id = '.$id, [1]);
        FileController::delete('content', $id);
    }
    public function setContent(Request $request)
    {
        DB::update('update content set 
            created_at = now(), 
            updated_at = now(), 
            description = "'.$request->description.'"
            where id = '.$request->id, [1]);
        return $request->id;
    }
    public function getContent(Request $request)
    {
        return DB::select('select * from content', [1]);
    }
}
