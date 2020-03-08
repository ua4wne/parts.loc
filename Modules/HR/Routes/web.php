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

Route::middleware(['auth'])->group(function () {
    //positions/ группа обработки роутов positions
    Route::group(['prefix' => 'positions'], function () {
        Route::get('/', ['uses' => 'PositionController@index', 'as' => 'positions']);
        //positions/add
        Route::match(['get', 'post'], '/add', ['uses' => 'PositionController@create', 'as' => 'positionAdd']);
        //positions/edit
        Route::match(['get', 'post', 'delete'], '/edit/{id}', ['uses' => 'PositionController@edit', 'as' => 'positionEdit']);
    });

    //personals/ группа обработки роутов personals
    Route::group(['prefix' => 'personals'], function () {
        Route::get('/', ['uses' => 'PersonalController@index', 'as' => 'personals']);
        //personals/add
        Route::match(['get', 'post'], '/add', ['uses' => 'PersonalController@create', 'as' => 'personalAdd']);
        //personals/edit
        Route::match(['get', 'post'], '/edit/{id}', ['uses' => 'PersonalController@edit', 'as' => 'personalEdit']);
        //personals/delete
        Route::post('/delete', ['uses' => 'PersonalController@destroy', 'as' => 'personalDel']);
    });
});
