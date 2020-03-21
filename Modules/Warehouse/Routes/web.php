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
        //goods/view
        Route::post('/view',['uses'=>'GoodController@view','as'=>'viewGood']);
        //goods/find
        Route::post('/find',['uses'=>'GoodController@find','as'=>'findGood']);
        //goods/edit
        Route::post('/edit',['uses'=>'GoodController@edit','as'=>'editGood']);
        //goods/delete
        Route::post('/delete',['uses'=>'GoodController@delete','as'=>'delGood']);
        //goods/import
        Route::post('/import', ['uses'=>'GoodController@download','as'=>'importGood']);
        //goods/export
        Route::post('/export',['uses'=>'GoodController@upload','as'=>'exportGood']);
        //goods/vendor
        Route::get('/vendor',['uses'=>'GoodController@ajaxData','as'=>'getCode']);
    });

    //categories/ группа обработки роутов categories
    Route::group(['prefix'=>'categories'], function(){
        Route::post('/',['uses'=>'CategoryController@index','as'=>'viewCategories']);

    });

    //inventories/ группа обработки роутов inventories
    Route::group(['prefix'=>'inventories'], function(){
        Route::get('/',['uses'=>'InventoryController@index','as'=>'inventories']);
        //inventories/add
        Route::match(['get','post'],'/add',['uses'=>'InventoryController@create','as'=>'inventoryAdd']);
        //inventories/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'InventoryController@edit','as'=>'inventoryEdit']);
        //inventories/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'InventoryController@show','as'=>'inventoryView']);
        //inventories/pos/add
        Route::post('/pos/add',['uses'=>'InventoryController@createPosition','as'=>'posInvAdd']);
        //inventories/pos/find
        Route::post('/pos/find',['uses'=>'InventoryController@findPosition','as'=>'findPosInv']);
        //inventories/pos/edit
        Route::post('/pos/edit',['uses'=>'InventoryController@editPosition','as'=>'posInvEdit']);
        //inventories/pos/delete
        Route::post('/pos/delete',['uses'=>'InventoryController@delPosition','as'=>'posInvDel']);
        //inventories/import
        Route::post('/import', ['uses'=>'InventoryController@download','as'=>'importInventory']);
        //inventories/write
        Route::post('/write', ['uses'=>'InventoryController@writeToStock','as'=>'writeInventory']);
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

    //units/ группа обработки роутов units
    Route::group(['prefix'=>'units'], function(){
        Route::get('/',['uses'=>'UnitController@index','as'=>'units']);
        //units/add
        Route::match(['get','post'],'/add',['uses'=>'UnitController@create','as'=>'unitAdd']);
        //units/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'UnitController@edit','as'=>'unitEdit']);
    });
});

