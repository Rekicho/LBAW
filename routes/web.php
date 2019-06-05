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

Route::post('api/bans', 'BanController@create');

Route::post('api/billingInfo', 'BillingInfoController@create');
Route::post('api/billingInfo/{id}', 'BillingInfoController@update');

Route::post('api/wishlist', 'WishListController@create');
Route::delete('api/wishlist/{id}', 'WishListController@delete');

Route::post('api/cart', 'CartController@create');
Route::post('api/cart/{id}', 'CartController@update');
Route::delete('api/cart/{id}', 'CartController@delete');

Route::put('api/products', 'ProductController@create');
Route::post('api/products/{id}', 'ProductController@update');

Route::put('api/categories', 'CategoryController@create');

Route::put('api/discounts', 'DiscountController@create');

Route::put('api/purchases/{id}', 'PurchaseLogController@create');

Route::put('api/reviews', 'ReviewController@create');
Route::post('api/reviews/{id}', 'ReviewController@update');

// Products
Route::get('product/{id}', 'ProductController@show');
Route::get('category/{id}', 'CategoryController@show');

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
Route::get('back-office/moderator/ajax/{type}', 'BackOffice\ModeratorController@getModeratorType')->where('type', 'users|reports|reviews');
Route::get('back-office/moderator', 'BackOffice\ModeratorController@show');
Route::get('back-office/stock/ajax/{type}', 'BackOffice\StockController@getStockType')->where('type', 'products|categories');
Route::get('back-office/stock', 'BackOffice\StockController@show');

Route::get('back-office/profile', 'UserController@showStaffProfile');
Route::get('search/', 'SearchController@show');
Route::get('back-office/accounting', 'BackOffice\AccountingController@show');

// Facebook login
Route::get('login/facebook', 'Auth\LoginController@redirectToProvider');
Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallback');