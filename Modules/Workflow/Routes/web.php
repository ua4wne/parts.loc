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
    //banks/ группа обработки роутов banks
    Route::group(['prefix'=>'banks'], function(){
        Route::get('/',['uses'=>'BankController@index','as'=>'banks']);
        //banks/add
        Route::match(['get','post'],'/add',['uses'=>'BankController@create','as'=>'bankAdd']);
        //banks/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'BankController@edit','as'=>'bankEdit']);
        //banks/accounts
        Route::get('/accounts/{id}',['uses'=>'BankAccountController@index','as'=>'bank_accounts']);
        //banks/accounts/add
        Route::match(['get','post'],'/accounts/add/{id}',['uses'=>'BankAccountController@create','as'=>'bank_accountAdd']);
        //banks/accounts/edit
        Route::match(['get','post','delete'],'/accounts/edit/{id}',['uses'=>'BankAccountController@edit','as'=>'bank_accountEdit']);
        //bik
        Route::get('/bik',['uses'=>'BankController@ajaxBik','as'=>'getBik']);
        //swift
        Route::get('/swift',['uses'=>'BankController@ajaxSwift','as'=>'getSwift']);
        //banks/accounts/find
        Route::post('/accounts/find', ['uses' => 'BankAccountController@findOrgAcc', 'as' => 'findOrgAcc']);
        //banks/fill
        Route::post('/fill',['uses'=>'BankController@fill','as'=>'bankFill']);
    });

    //firms/ группа обработки роутов firms
    Route::group(['prefix'=>'firms'], function(){
        Route::get('/',['uses'=>'FirmController@index','as'=>'firms']);
        //firms/add
        Route::match(['get','post'],'/add',['uses'=>'FirmController@create','as'=>'firmAdd']);
        //firms/edit
        Route::match(['get','post'],'/edit/{id}',['uses'=>'FirmController@edit','as'=>'firmEdit']);
        //firms/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'FirmController@show','as'=>'firmView']);
        //firms/contact/edit
        Route::post('/contact/edit',['uses'=>'FirmController@contact_edit','as'=>'firmContactEdit']);
        //firms/del
        Route::post('/del',['uses'=>'FirmController@delete','as'=>'firmDelete']);
        //firms/contracts
        Route::get('/contracts/{id}',['uses'=>'ContractController@index','as'=>'contracts']);
        //firms/contracts/add
        Route::match(['get','post'],'/contracts/add/{id}',['uses'=>'ContractController@create','as'=>'contractAdd']);
        //firms/contracts/edit
        Route::match(['get','post','delete'],'/contracts/edit/{id}',['uses'=>'ContractController@edit','as'=>'contractEdit']);
        //firms/fill
        Route::post('/fill',['uses'=>'FirmController@fill','as'=>'firmFill']);
    });

    //orders/ группа обработки роутов orders
    Route::group(['prefix'=>'orders'], function(){
        Route::get('/',['uses'=>'OrderController@index','as'=>'orders']);
        //orders/add
        Route::match(['get','post'],'/add',['uses'=>'OrderController@create','as'=>'orderAdd']);
        //orders/edit
        Route::match(['get','post'],'/edit/{id}',['uses'=>'OrderController@edit','as'=>'orderEdit']);
        //orders/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'OrderController@show','as'=>'orderView']);
        //orders/del
        Route::post('/del',['uses'=>'OrderController@delete','as'=>'orderDelete']);
        //orders/addpos
        Route::post('/addpos',['uses'=>'OrderController@addPosition','as'=>'addOrderPos']);
        //orders/delpos
        Route::post('/delpos',['uses'=>'OrderController@delPosition','as'=>'delOrderPos']);
        //orders/delerrpos
        Route::post('/delerrpos',['uses'=>'OrderController@delErrPosition','as'=>'delErrPos']);
        //orders/getfirm
        Route::get('/getfirm',['uses'=>'OrderController@ajaxData','as'=>'getFirm']);
        //orders/find_contract
        Route::post('/find_contract',['uses'=>'OrderController@findContract','as'=>'findContract']);
        //orders/find_good
        Route::get('/find_good',['uses'=>'OrderController@findGood','as'=>'searchGood']);
        //orders/import
        Route::post('/import', ['uses'=>'OrderController@download','as'=>'importOrderPos']);
        //orders/get_spec
        Route::post('/get_spec',['uses'=>'OrderController@getSpecifications','as'=>'getSpecPos']);
        //orders/set_spec
        Route::post('/set_spec',['uses'=>'OrderController@setSpecifications','as'=>'setSpecPos']);
        //orders/find_by_vendor
        Route::post('/find_by_vendor',['uses'=>'OrderController@findByVendor','as'=>'searchByVendor']);
        //orders/find_by_name
        Route::post('/find_by_name',['uses'=>'OrderController@findByName','as'=>'searchByName']);
        //orders/spec_by_vendor
        Route::post('/spec_by_vendor',['uses'=>'OrderController@specByVendor','as'=>'specByVendor']);
        //orders/edit-err-pos
        Route::post('/edit-err-pos',['uses'=>'OrderController@editErrPos','as'=>'editErrPos']);
        //orders/new_purchase
        Route::get('/new_purchase/{id}',['uses'=>'OrderController@newPurchase','as'=>'newPurchase']);
    });

    //purchases/ группа обработки роутов purchases
    Route::group(['prefix'=>'purchases'], function(){
        Route::get('/',['uses'=>'PurchaseController@index','as'=>'purchases']);
        //purchases/add
        Route::match(['get','post'],'/add',['uses'=>'PurchaseController@create','as'=>'purchaseAdd']);
        //purchases/edit
        Route::match(['get','post'],'/edit/{id}',['uses'=>'PurchaseController@edit','as'=>'purchaseEdit']);
        //purchases/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'PurchaseController@show','as'=>'purchaseView']);
        //purchases/del
        Route::post('/del',['uses'=>'PurchaseController@delete','as'=>'purchaseDelete']);
        //purchases/addpos
        Route::post('/addpos',['uses'=>'PurchaseController@addPosition','as'=>'addPurchasePos']);
        //purchases/delpos
        Route::post('/delpos',['uses'=>'PurchaseController@delPosition','as'=>'delPurchasePos']);
        //purchases/import
        Route::post('/import', ['uses'=>'PurchaseController@download','as'=>'importPurchasePos']);
        //purchases/find_by_order
        Route::get('/find_by_order',['uses'=>'PurchaseController@findByOrder','as'=>'searchByOrder']);
        //purchases/get_order_pos
        Route::post('/get_order_pos',['uses'=>'PurchaseController@getOrderPos','as'=>'getOrderPos']);
        //purchases/pos_edit
        Route::post('/pos_edit',['uses'=>'PurchaseController@PosEdit','as'=>'PurchasePosEdit']);
    });

});