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

// Route::get('/', function () {
//     return view('welcome');
// });appkey face 5064ebc8402c4434aaabbbd90e471f63

Route::get('/', 'AIController@detectFaces');
Route::get('/v2', 'AIController@detectFacesV2');
Route::get('/v3', 'AIController@index');
Route::post('/v4', 'AIController@snap')->name('snap');