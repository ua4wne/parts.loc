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

    //expenses/ группа обработки роутов справочника expenses
    Route::group(['prefix'=>'expenses'], function(){
        Route::get('/',['uses'=>'ExpenseController@index','as'=>'expenses']);
        //expenses/add
        Route::match(['get','post'],'/add',['uses'=>'ExpenseController@create','as'=>'expenseAdd']);
        //expenses/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'ExpenseController@edit','as'=>'expenseEdit']);
    });

    //cars/ группа обработки роутов справочника cars
    Route::group(['prefix'=>'cars'], function(){
        Route::get('/',['uses'=>'CarController@index','as'=>'cars']);
        //cars/add
        Route::match(['get','post'],'/add',['uses'=>'CarController@create','as'=>'carAdd']);
        //cars/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'CarController@edit','as'=>'carEdit']);
    });

    //countries/ группа обработки роутов справочника countries
    Route::group(['prefix'=>'countries'], function(){
        Route::get('/',['uses'=>'CountryController@index','as'=>'countries']);
        //countries/add
        Route::match(['get','post'],'/add',['uses'=>'CountryController@create','as'=>'countryAdd']);
        //countries/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'CountryController@edit','as'=>'countryEdit']);
        //countries/import
        Route::post('/import', ['uses'=>'CountryController@download','as'=>'importCountries']);
        //countries/export
        Route::get('/export',['uses'=>'CountryController@upload','as'=>'exportCountries']);
    });

    //delivery-methods/ группа обработки роутов справочника delivery-methods
    Route::group(['prefix'=>'delivery-methods'], function(){
        Route::get('/',['uses'=>'DeliveryMethodController@index','as'=>'methods']);
        //delivery-methods/add
        Route::match(['get','post'],'/add',['uses'=>'DeliveryMethodController@create','as'=>'methodAdd']);
        //delivery-methods/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'DeliveryMethodController@edit','as'=>'methodEdit']);
    });

    //deliveries/ группа обработки роутов справочника deliveries
    Route::group(['prefix'=>'deliveries'], function(){
        Route::get('/',['uses'=>'DeliveryController@index','as'=>'deliveries']);
        //deliveries/add
        Route::match(['get','post'],'/add',['uses'=>'DeliveryController@create','as'=>'deliveryAdd']);
        //deliveries/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'DeliveryController@edit','as'=>'deliveryEdit']);
    });

    //hopers/ группа обработки роутов справочника hopers
    Route::group(['prefix'=>'hopers'], function(){
        Route::get('/',['uses'=>'HoperationController@index','as'=>'hopers']);
        //hopers/add
        Route::match(['get','post'],'/add',['uses'=>'HoperationController@create','as'=>'hoperAdd']);
        //hopers/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'HoperationController@edit','as'=>'hoperEdit']);
    });

    //stats/ группа обработки роутов справочника stats
    Route::group(['prefix'=>'stats'], function(){
        Route::get('/',['uses'=>'StatusController@index','as'=>'stats']);
        //stats/add
        Route::match(['get','post'],'/add',['uses'=>'StatusController@create','as'=>'statusAdd']);
        //stats/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'StatusController@edit','as'=>'statusEdit']);
    });

    //firm-types/ группа обработки роутов справочника firm-types
    Route::group(['prefix'=>'firm-types'], function(){
        Route::get('/',['uses'=>'FirmTypeController@index','as'=>'typefirms']);
        //firm-types/add
        Route::match(['get','post'],'/add',['uses'=>'FirmTypeController@create','as'=>'firm_typeAdd']);
        //firm-types/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'FirmTypeController@edit','as'=>'firm_typeEdit']);
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
