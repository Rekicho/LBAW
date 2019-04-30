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

// Products
Route::get('product/{id}', 'ProductController@show');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// User area
Route::get('profile', 'UserController@showProfile')->middleware('auth');;

// Back-office
Route::get('back-office/admin', 'BackOffice\AdminController@show');