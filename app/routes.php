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
Route::get('login', array('as' => 'login', 'uses' => 'UserController@login'));
Route::post('/login', array('as' => 'login', 'uses' => 'UserController@handleLogin'));
Route::get('/profile', array('as' => 'profile', 'uses' => 'UserController@profile'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'UserController@logout'));
Route::resource('user', 'UserController'); 




