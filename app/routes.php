<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('', array('as' => 'home', 'uses' => 'HomeController@index'));
Route::post('', array('as' => 'home', 'uses' => 'HomeController@index'));
Route::get('login', array('as' => 'login', 'uses' => 'UserController@login'));
Route::post('/login', array('as' => 'login', 'uses' => 'UserController@handleLogin'));
Route::get('/profile', array('as' => 'profile', 'uses' => 'UserController@profile'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'UserController@logout'));
Route::resource('user', 'UserController'); 
Route::get('/admin', array('as' => 'admin', 'uses' => 'UserController@admin'));

Route::get('password/reset', array(
  'uses' => 'PasswordController@remind',
  'as' => 'password.remind'
));
Route::post('password/reset', array(
  'uses' => 'PasswordController@request',
  'as' => 'password.request'
));
Route::get('password/reset/{token}', array(
  'uses' => 'PasswordController@reset',
  'as' => 'password.reset'
));
Route::post('password/reset/{token}', array(
  'uses' => 'PasswordController@update',
  'as' => 'password.update'
));

Route::get('/history', array('as' => 'history', 'uses' => 'HomeController@history'));
Route::get('/getquery', array('as' => 'getquery', 'uses' => 'HomeController@getQuery'));


