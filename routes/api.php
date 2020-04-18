<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('guest:api')->group(function () {
    Route::post('/login','AuthController@login')->name('api.login');
	Route::post('/register','AuthController@register')->name('api.register');
});

//home routes
 Route::get('/bestsellers','HomeController@bestseller')->name('bestseller');
 Route::get('/arabic-bestsellers','HomeController@arabicBestseller')->name('arabic_bestseller');
 Route::get('/teens-bestsellers','HomeController@teensBestseller')->name('teens_bestseller');

 Route::get('/popular','HomeController@popular')->name('popular');
 Route::get('/sliders','HomeController@sliders')->name('sliders');

// home routes

//shop routes 


Route::get('/categories/{category?}','ShopController@categories')->name('categories');
Route::get('/authors','ShopController@authors')->name('authors');
Route::get('/languages','ShopController@languages')->name('languages');
Route::get('/ages','ShopController@ages')->name('ages');
Route::get('/products','ShopController@products')->name('products');
Route::get('/product/{id}','ShopController@product')->name('product');
Route::get('/checkout','ShopController@checkout')->name('checkout');

//shop routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
