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
            $id = DB::table('products_stock')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
                'name' => "$request->name",
                'posInfo_size' => "$request->posInfo_size",
                'posInfo_flowers' =>  $request->posInfo_flowers,
                'posInfo_colors' =>  $request->posInfo_colors,
                'posInfo_boxColor' =>  $request->posInfo_boxColor,
                'description' =>  $request->description,
                'price' => $request->price,
                'userid' => $request->userid
            ]);
        }
        else {
            if ($request->available == false) $request->available = 0;
            else $request->available = 1;
            if ($request->price_premium == '') $request->price_premium = $request->price;
            if ($request->price_vip == '') $request->price_vip = $request->price;
            $id = DB::table('products_stock')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
                'name' => "$request->name",
                'description' => "$request->description",
                'type' =>  $request->type,
                'sub' =>  $request->sub,
                'available' =>  $request->available,
                'price' => $request->price,
                'price_premium' => $request->price_premium,
                'price_vip' => $request->price_vip
            ]);
        }
        return $id;
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
            if ($request->available == false) $request->available = 0;
            else $request->available = 1;

            DB::update('update products_stock set
            updated_at = now(), 
            name = "'.$request->name.'",
            description = "'.$request->description.'",
            price = '.$request->price.', 
            price_premium = '.$request->price_premium.',
            price_vip = '.$request->price_vip.',
            available = '.$request->available.'
            where id = '.$request->id, [1]);
        }
        return $request->id;
    }

    public function getProductsById(Request $request, $id)
    {
        $products[0] = DB::table('products_stock')
            ->select('id', 'name', 'description', 'type', 'sub', 'available', 'price', 'price_premium', 'price_vip', 'imgext', $id)
            ->get();
        //$products[0] = DB::select('select id, name, desc, `'.$id.'` from products_stock', [1]);
        $products[1] = DB::select('select * from products', [1]);
        return $products;
    }

    public function getAllProducts(Request $request, $id)
    {
        $products[0] = DB::table('products_stock')
            ->select('id', 'name', 'description', 'type', 'sub', 'available', 'price', 'price_premium', 'price_vip', 'imgext', $id)
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
        if ($type == 0) $type = 'stock';
        if ($type == 1) $type = 'store';
        if ($type == 'stock') DB::delete('delete from products_stock where id = '.$id, [1]);
        if ($type == 'store') DB::delete('delete from products where id = '.$id, [1]);
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
        $cat_arr = DB::select('select id,name from categories', [1]);
        // Looping through all categories to add subcategories
        foreach ($cat_arr as $catinfo) {
            $start = array('id' => $catinfo->id, 'name' => $catinfo->name, 'subs' => []);
            $subcat_arr = DB::select('SELECT id, name FROM subcategories WHERE parent = '.$catinfo->id, [1]);
            // Adding subcategories
            foreach ($subcat_arr as $subcatinfo) {
                array_push($start['subs'], array( 'id' => $subcatinfo->id, 'name' => $subcatinfo->name));
            }
            array_push($result, $start);
        }
        return $result;
    }

    public function addCategory(Request $request)
    {
        // Adding category
        $query = DB::insert('INSERT INTO categories (id, name) VALUES ('.$request->id.',"'.$request->name.'")', [1]);
        return '$query';
    }
    public function editCategory(Request $request)
    {
        DB::update('update categories set 
            name = "'.$request->name.'"
            where id = '.$request->id, [1]);
        return $request->id;
    }
    public function addSubCategory(Request $request)
    {
        // Adding subcategory, parent id sent in url
        $query = DB::insert('INSERT INTO subcategories (name, parent) VALUES ("'.$request->name.'", '.$request->id.')', [1]);
        return '$query';
    }
    public function editSubCategory(Request $request)
    {
        DB::update('update subcategories set 
            name = "'.$request->name.'"
            where id = '.$request->id, [1]);
        return $request->id;
    }
    public function deleteCategory($id)
    {
        // Deleting category
        DB::delete('delete from categories where id = '.$id, [1]);
        $query2 = DB::select('select id from products_stock where type = '.$id, [1]);
        foreach ($query2[0] as $key => $value) {
            $this->deleteProduct('stock', $value);
        }
        return '$query';
    }

    public function deleteSubCategory($id, $parent)
    {
        // Deleting category
        DB::delete('delete from subcategories where id = '.$id.' and parent = '.$parent, [1]);
        $query2 = DB::select('select id from products_stock where sub = '.$id.' and type = '.$parent, [1]);
        foreach ($query2[0] as $key => $value) {
            $this->deleteProduct('stock', $value);
        }
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
