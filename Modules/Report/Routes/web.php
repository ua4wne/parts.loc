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

Route::middleware(['auth'])->group(function() {

    //reports/ группа обработки роутов reports
    Route::group(['prefix'=>'reports'], function(){

        Route::match(['get','post'],'/stock-report',['uses'=>'StockController@index','as'=>'stock-report']);
        Route::post('/stock-pie',['uses'=>'StockController@pie_graph','as'=>'stock-pie']);
        Route::post('/stock-table',['uses'=>'StockController@table','as'=>'stock-table']);

    });

});


