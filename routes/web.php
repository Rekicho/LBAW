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

Route::post('api/cart', 'CartController@create');
Route::post('api/cart/{id}', 'CartController@update');
Route::delete('api/cart/{id}', 'CartController@delete');

// Products
Route::get('product/{id}', 'ProductController@show');

// Authentication
Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// User area
Route::get('profile', 'UserController@showProfile')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::get('cart', 'CartController@show');
	Route::get('checkout', 'CheckoutController@show');
	Route::get('buy', 'CheckoutController@buy');
});

// Back-office
Route::get('back-office/admin', 'BackOffice\AdminController@show');