<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "public" middleware group.
|
*/

Route::get('/', 'PublicWebsiteController@index');
Route::get('/home', 'PublicWebsiteController@index');