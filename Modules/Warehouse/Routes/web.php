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
    //goods/ группа обработки роутов goods
    Route::group(['prefix'=>'goods'], function(){
        Route::get('/',['uses'=>'GoodController@index','as'=>'goods']);
        //goods/add
        Route::match(['get','post'],'/add',['uses'=>'GoodController@create','as'=>'goodAdd']);
        //goods/edit
        Route::match(['get','post'],'/edit/{id}',['uses'=>'GoodController@edit','as'=>'goodEdit']);
        //goods/delete
        Route::post('/delete',['uses'=>'GoodController@delete','as'=>'delGood']);

    });

    //categories/ группа обработки роутов categories
    Route::group(['prefix'=>'categories'], function(){
        Route::post('/',['uses'=>'CategoryController@index','as'=>'viewCategories']);

    });

    //warehouses/ группа обработки роутов warehouses
    Route::group(['prefix'=>'warehouses'], function(){
        Route::get('/',['uses'=>'WarehouseController@index','as'=>'warehouses']);
        //warehouses/add
        Route::match(['get','post'],'/add',['uses'=>'WarehouseController@create','as'=>'warehouseAdd']);
        //warehouses/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'WarehouseController@edit','as'=>'warehouseEdit']);
    });

    //groups/ группа обработки роутов groups
    Route::group(['prefix'=>'groups'], function(){
        Route::get('/',['uses'=>'GroupController@index','as'=>'groups']);
        //groups/add
        Route::match(['get','post'],'/add',['uses'=>'GroupController@create','as'=>'groupAdd']);
        //groups/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'GroupController@edit','as'=>'groupEdit']);
    });
});

