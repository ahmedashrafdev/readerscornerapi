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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
