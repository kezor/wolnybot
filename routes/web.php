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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/players/create', 'PlayersController@create')->name('player.create');
Route::post('/players/store', 'PlayersController@store')->name('player.store');

Route::get('players/{id}/updateData', 'PlayersController@updateData')->name('player.updateData');
Route::get('players/{id}', 'PlayersController@show')->name('player.show');

Route::post('players/{id}/task', 'PlayersController@addTask')->name('player.addTask');
Route::get('tasks/{id}/changeStatus/{status}', 'TasksController@changeStatus')->name('tasks.changeStatus');

