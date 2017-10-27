<?php

use App\Events\eventTrigger;

Route::get('/', 'HomeController@index');

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

Auth::routes();

Route::get('/feemng', 'FeemngController@index')->name('feemng');
Route::get('/getassetfeedata/{product}', 'FeemngController@getAssetFeeData')->name('getassetfeedata');
Route::post('/feesave', 'FeemngController@FeeSave')->name('feesave');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/ordermarket', 'OrderController@index')->name('ordermarket');
Route::post('/setorder', "OrderController@setOrder")->name('setorder');
Route::post('/setlimit', "OrderController@setLimit")->name('setlimit');
Route::post('/setstop', "OrderController@setStop")->name('setstop');
Route::get('/getorderbook', "OrderController@getOrderBook")->name('getorderbook');
Route::get('/ordersimulate', "OrderController@index")->name('ordersimulate');
Route::post('/addorder', "OrderController@addOrder")->name('addorder');

Route::post('assetdeposit/{product}', 'OrderController@AssetDeposit')->name('assetdeposit');
Route::post('assetwithdraw/{product}', 'OrderController@AssetWithdraw')->name('assetwithdraw');

Route::get('/fireEvent', function(){
    event(new eventTrigger());
});

/** REST API  **/
Route::get('/getfilldata/{product}', "OrderController@getFillDataOfOrder")->name('getfilldata');

Route::get('/getopenorders/{product}', "OrderController@getOpenOrders")->name('getopenorders');
Route::get('/getfilledlist/{product}', "OrderController@getFilledList")->name('getfilledlist');
Route::get('/gettradeprice/{product}', "OrderController@getTradePrice")->name('gettradeprice');
Route::get('/gettradehistory/{product}', "OrderController@getTradeHistory")->name('gettradehistory');
Route::get('/getorderbooklist/{product}', "OrderController@getOrderBookList")->name('getorderbooklist');
Route::get('/getassetbalance/{product}', "OrderController@getAssetBalance")->name('getassetbalance');

Route::get('/testlink', "OrderController@makeFill")->name('testlink');
