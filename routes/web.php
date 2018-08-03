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

Auth::routes();

Route::get('/tasks', 'TaskController@index')->name('tasks');
Route::get('/tasks/{task}', 'TaskController@show')->name('task');
Route::get('/tasks/create', 'TaskController@create')->name('create-task');
Route::post('/tasks', 'TaskController@store');
Route::patch('/tasks/{task}', 'TaskController@update');
Route::patch('/tasks/{task}/status', 'TaskStatusController@update');
