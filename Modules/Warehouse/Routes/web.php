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
        //goods/transfer
        Route::post('/transfer',['uses'=>'GoodController@transfer','as'=>'transferGood']);
        //goods/vendor
        Route::get('/vendor',['uses'=>'GoodController@ajaxData','as'=>'getCode']);
        //goods/analog
        Route::get('/analog',['uses'=>'GoodController@getAnalog','as'=>'getAnalog']);
        //goods/catalog_num
        Route::get('/catalog_num',['uses'=>'GoodController@getCatalogNum','as'=>'getCatalogNum']);
        //goods/del-space
        Route::post('/del-space',['uses'=>'GoodController@delSpace','as'=>'delSpace']);
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

    //specifications/ группа обработки роутов specifications
    Route::group(['prefix'=>'specifications'], function(){
        Route::get('/view/{id}',['uses'=>'SpecificationController@index','as'=>'specifications']);
        //specifications/add
        Route::match(['get','post'],'/add/{id}',['uses'=>'SpecificationController@create','as'=>'spfcAdd']);
        //specifications/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'SpecificationController@edit','as'=>'spfcEdit']);
    });

    //brands/ группа обработки роутов brands
    Route::group(['prefix'=>'brands'], function(){
        Route::get('/',['uses'=>'BrandController@index','as'=>'brands']);
        //brands/add
        Route::match(['get','post'],'/add',['uses'=>'BrandController@create','as'=>'brandAdd']);
        //brands/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'BrandController@edit','as'=>'brandEdit']);
        //brands/import
        Route::post('/import', ['uses'=>'BrandController@download','as'=>'importBrand']);
    });

    //specifications/ группа обработки роутов specifications
//    Route::group(['prefix'=>'specifications'], function(){
//        Route::get('/',['uses'=>'SpecificationController@index','as'=>'specifications']);
//        //specifications/add
//        Route::match(['get','post'],'/add',['uses'=>'SpecificationController@create','as'=>'specAdd']);
//        //specifications/edit
//        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'SpecificationController@edit','as'=>'specEdit']);
//    });

    //inventories/ группа обработки роутов inventories
    Route::group(['prefix'=>'wh_corrects'], function(){
        Route::get('/',['uses'=>'WhCorrectController@index','as'=>'wh_corrects']);
        //wh_corrects/add
        Route::match(['get','post'],'/add',['uses'=>'WhCorrectController@create','as'=>'wh_correctsAdd']);
        //wh_corrects/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'WhCorrectController@edit','as'=>'wh_correctEdit']);
        //wh_corrects/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'WhCorrectController@show','as'=>'wh_correctsView']);
        //wh_corrects/pos/add
        Route::post('/pos/add',['uses'=>'WhCorrectController@createPosition','as'=>'posWhcAdd']);
        //wh_corrects/pos/find
        Route::post('/pos/find',['uses'=>'WhCorrectController@findPosition','as'=>'findPosWhc']);
        //wh_corrects/pos/edit
        Route::post('/pos/edit',['uses'=>'WhCorrectController@editPosition','as'=>'posWhcEdit']);
        //wh_corrects/pos/delete
        Route::post('/pos/delete',['uses'=>'WhCorrectController@delPosition','as'=>'posWhcDel']);
        //wh_corrects/import
        Route::post('/import', ['uses'=>'WhCorrectController@download','as'=>'importWhCorrect']);
        //wh_corrects/export
        Route::post('/export', ['uses'=>'WhCorrectController@upload','as'=>'exportWhCorrect']);
        //wh_corrects/write
        Route::post('/write', ['uses'=>'WhCorrectController@writeToStock','as'=>'writeWhCorrect']);
    });

    //locations/ группа обработки роутов locations
    Route::group(['prefix'=>'locations'], function(){
        Route::get('/',['uses'=>'LocationController@index','as'=>'locations']);
        //locations/add
        Route::match(['get','post'],'/add',['uses'=>'LocationController@create','as'=>'locationAdd']);
        //locations/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'LocationController@edit','as'=>'locationEdit']);
    });
});

