<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('ajax',function() {
   return view('message');
});

Route::get('login', 'Auth\LoginController@isLoggedIn')->name('login');

Route::get('register/request', 'Auth\RegisterController@requestInvitation')->name('requestInvitation');
Route::post('invitations', 'InviteController@store')->middleware('guest')->name('storeInvitation');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
