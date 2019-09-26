<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Use App\User;
Route::group(['middleware' => 'cors'], function () {

    Route::group(['middleware' => ['json.response']], function () {

        Route::middleware('auth:api')->get('/user', function (Request $request) {
            return $request->user();
        });

        // public routes
        Route::post('/login', 'Api\AuthController@login')->name('login.api');
        Route::post('/register', 'Api\AuthController@register')->name('register.api');

        // private routes
        Route::middleware('auth:api')->group(function () {
            Route::get('/logout', 'Api\AuthController@logout')->name('logout');
        });

    });


// Get and post storage information
    Route::get('store/products/{id}', 'StoreController@getProductsById');
    Route::get('store/products', 'StoreController@getAllProducts');

    Route::get('store/categories', 'StoreController@getCategories');
    Route::post('store/categories/0', 'StoreController@addCategory');
    Route::post('store/categories/1', 'StoreController@addSubCategory');
    Route::put('store/categories/0', 'StoreController@editCategory');
    Route::put('store/categories/1', 'StoreController@editSubCategory');
    Route::delete('store/categories/{id}', 'StoreController@deleteCategory');
    Route::delete('store/categories/{id}/{parent}', 'StoreController@deleteSubCategory');

    Route::post('store/stock/-', 'StoreController@pullStocks');
    Route::post('store/stock/+', 'StoreController@pushStocks');
    Route::post('store/add/{type}', 'StoreController@addProduct');
    Route::post('store/set/{type}', 'StoreController@setProduct');
    Route::delete('store/delete/{type}/{id}', 'StoreController@deleteProduct');
    Route::get('store/site/{id}', 'StoreController@getSiteStore');
    Route::get('store/city/{city}', 'StoreController@getStoreByCity');

    Route::get('requests', 'ShopRequestController@getAllRequests');
    Route::get('requests/{id}', 'ShopRequestController@getRequestsByUser');
    Route::put('request/{id}', 'ShopRequestController@editRequest');
    Route::post('requests', 'ShopRequestController@addRequest');
    Route::post('requests/delete', 'ShopRequestController@deleteRequest');

    Route::get('content', 'StoreController@getContent');
    Route::post('content/{id}', 'StoreController@setContent');
    Route::post('content', 'StoreController@addContent');
    Route::delete('content/{id}', 'StoreController@deleteContent');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendPasswordReset')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ForgotPasswordController@resetPasswordRedirect')->name('password.reset');
    Route::post('password/reset', 'Auth\ForgotPasswordController@reset')->name('password.update');


// Get and post user information
    Route::get('user/checkAuth/{id}', 'Auth\AuthController@getKey')->name('checkAuth');
    Route::post('user/login', 'Auth\AuthController@login');
    Route::post('user/logout', 'Auth\AuthController@logout')->name('logout');
    Route::post('user/changePwd/{id}', 'Auth\AuthController@changePassword');
    Route::post('user/invite', 'InviteController@sendInviteLinkEmail')->name('requestInvitation');
    Route::post('user/register', 'Auth\AuthController@register')->name('register');

    Route::get('user/info/{id}', 'UserInfoController@getInfo');
    Route::post('user/info/{id}', 'UserInfoController@setInfo');
    Route::get('user/city', 'UserInfoController@getUserByCity');
    Route::get('user/cities', 'UserInfoController@getCities');
    Route::get('user/pins', 'UserInfoController@getPartnerMap');
    Route::get('user/partners', 'UserInfoController@getPartners');
    Route::post('user/partners', 'UserInfoController@setPartner');
    Route::delete('user/partner/{id}/{email}', 'Auth\AuthController@deleteRow');

    Route::get('user/stats/{id}', 'UserInfoController@getStats');
    Route::post('user/stats', 'UserInfoController@setStats');
    Route::get('user/history/{id}', 'UserInfoController@getHistory');
    Route::get('user/socials/{id}', 'UserInfoController@getSocials');
    Route::get('user/socials', 'UserInfoController@getAllSocials');
    Route::post('user/socials/{id}', 'UserInfoController@setSocials');

    Route::put('user/lastsale/{id}', 'UserInfoController@setLastSale');
    Route::post('user/notify', 'NotifyController@sendNotifyEmail');

    Route::post('file', 'FileController@store');
    Route::get('file/all/{type}', 'FileController@showAll');
    Route::get('file/one/{type}/{id}', 'FileController@showOne');

    Route::get('file/oneblob/{type}/{id}', 'FileController@showOneBlob');
    Route::get('file/userproducts/{type}/{id}', 'FileController@showProductsByUser');
    Route::get('file/profile/shown', 'FileController@showPartnersByRole');

//Route::get('user/{id}', function($id) {return User::find($id);});
//Route::post('user/new', function(Request $request) {return Product::create($request->all);});

});
