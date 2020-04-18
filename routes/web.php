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

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//activate
Route::get('/activate','Auth\LoginController@activate');

Route::middleware(['auth'])->group(function(){
    Route::get('/', 'MainController@index')->name('main');
    //profiles/ группа обработки роутов profiles
    Route::group(['prefix'=>'profiles'], function(){
        Route::get('/',['uses'=>'ProfileController@index','as'=>'profiles']);
        //profiles/edit
        Route::post('/edit',['uses'=>'ProfileController@edit','as'=>'editProfile']);
        //profiles/avatar
        Route::post('/avatar',['uses'=>'ProfileController@avatar','as'=>'editAvatar']);
        //profiles/reset-pass
        Route::post('/newpass',['uses'=>'ProfileController@newPass','as'=>'newPass']);
    });

    //orgforms/ группа обработки роутов orgforms
    Route::group(['prefix'=>'orgforms'], function(){
        Route::get('/',['uses'=>'OrgFormController@index','as'=>'orgforms']);
        //orgforms/add
        Route::match(['get','post'],'/add',['uses'=>'OrgFormController@create','as'=>'orgformAdd']);
        //orgforms/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'OrgFormController@edit','as'=>'orgformEdit']);

    });

    //organisations/ группа обработки роутов organisations
    Route::group(['prefix' => 'organisations'], function () {
        Route::get('/', ['uses' => 'OrganisationController@index', 'as' => 'orgs']);
        //organisations/add
        Route::match(['get', 'post'], '/add', ['uses' => 'OrganisationController@create', 'as' => 'orgAdd']);
        //organisations/edit
        Route::match(['get', 'post','delete'], '/edit/{id}', ['uses' => 'OrganisationController@edit', 'as' => 'orgEdit']);
        //organisations/view
        Route::get('/view/{id}', ['uses' => 'OrganisationController@view', 'as' => 'orgView']);
    });

    //currency/ группа обработки роутов справочника currency
    Route::group(['prefix'=>'currency'], function(){
        Route::get('/',['uses'=>'CurrencyController@index','as'=>'currency']);
        //currency/add
        Route::match(['get','post'],'/add',['uses'=>'CurrencyController@create','as'=>'currencyAdd']);
        //currency/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'CurrencyController@edit','as'=>'currencyEdit']);
    });

    //prices/ группа обработки роутов prices
    Route::group(['prefix'=>'prices'], function(){
        Route::get('/',['uses'=>'PriceController@index','as'=>'prices']);
        //prices/add
        Route::match(['get','post'],'/add',['uses'=>'PriceController@create','as'=>'priceAdd']);
        //prices/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'PriceController@edit','as'=>'priceEdit']);
        //prices/view
        Route::match(['get','post'],'/view/{id}',['uses'=>'PriceController@show','as'=>'priceView']);
        //prices/pos/add
        Route::post('/pos/add',['uses'=>'PriceController@createPosition','as'=>'posPriceAdd']);
        //prices/pos/find
        Route::post('/pos/find',['uses'=>'PriceController@findPosition','as'=>'findPosition']);
        //prices/pos/edit
        Route::post('/pos/edit',['uses'=>'PriceController@editPosition','as'=>'posPriceEdit']);
        //prices/pos/delete
        Route::post('/pos/delete',['uses'=>'PriceController@delPosition','as'=>'posPriceDel']);
        //prices/import
        Route::post('/import', ['uses'=>'PriceController@download','as'=>'importPrice']);
        //prices/export
        Route::get('/export/{id}',['uses'=>'PriceController@upload','as'=>'exportPrice']);
        //prices/transfer
        Route::post('/transfer',['uses'=>'PriceController@transfer','as'=>'transferPrice']);
    });
});
