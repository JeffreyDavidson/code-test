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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('products', 'API\ProductsController@index');
    Route::post('products', 'API\ProductsController@store');
    Route::put('products/{product}', 'API\ProductsController@update');
    Route::delete('products/{product}', 'API\ProductsController@destroy');
    Route::get('products/{product}', 'API\ProductsController@show');
    Route::post('products/{product}/attach', 'API\UserProductsController@store');
    Route::delete('products/{product}/detach', 'API\UserProductsController@destroy');
    Route::get('users/{user}/products', 'API\UserProductsController@index');
});


Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
