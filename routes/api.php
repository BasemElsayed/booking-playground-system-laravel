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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'API\UserController@login');

Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');
    Route::post('viewBooking', 'API\BookingController@showPerUser');
    Route::post('logout','API\UserController@logoutApi');
    Route::get('findUserById/{id}', 'API\UserController@findUserById');

});

Route::post('addPlayground', 'API\PlaygroundController@addPlayground');
Route::post('book', 'API\BookingController@create');

Route::post('editPlayground/{id}', 'API\PlaygroundController@update');
Route::get('deletePlayground/{id}', 'API\PlaygroundController@destroy');

Route::post('editUser/{id}', 'API\UserController@update');
Route::get('deleteUser/{id}', 'API\UserController@destroy');

Route::get('viewPlaygrounds', 'API\PlaygroundController@viewAll');
Route::get('viewPlayground/{id}', 'API\PlaygroundController@show');
Route::get('unapprovedBookings', 'API\BookingController@viewUnapproved');
Route::get('viewBookings', 'API\BookingController@viewAll');

Route::get('approveBooking/{id}', 'API\BookingController@approve');

Route::get('deleteBooking/{id}', 'API\BookingController@destroy');




