<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|      $user->is_enabled = $request->input('is_enabled') === 'true' ? true : false;

| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomePageController@show');


// Static pages
Route::get('about', 'AboutController@showAbout');
Route::get('faq', 'AboutController@showFaq');
Route::get('contact', 'AboutController@showContact');

// API
Route::post('api/users', 'UserController@create');
Route::post('api/users/{id}', 'UserController@update');

Route::post('api/billingInfo', 'BillingInfoController@create');
Route::post('api/billingInfo/{id}', 'BillingInfoController@update');

Route::post('api/wishlist', 'WishListController@create');
Route::delete('api/wishlist/{id}', 'WishListController@delete');

Route::put('api/products', 'ProductController@create');
Route::post('api/products/{id}', 'ProductController@update');

// Products
Route::get('product/{id}', 'ProductController@show');

// Authentication
Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// User area
Route::get('profile', 'UserController@showProfile')->middleware('auth');

// Back-office
Route::get('back-office/admin', 'BackOffice\AdminController@show');
Route::get('back-office/moderator', 'BackOffice\ModeratorController@show');
Route::get('back-office/stock', 'BackOffice\StockController@show');