<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('app');
});
Route::get('dashboard/', function () {
    return view('app');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::get('home', array('uses' => 'HomeController@index'));

// route to process the form
//Route::post('login', array('uses' => 'HomeController@doLogin'));


	
Route::post('login', array('uses' => 'HomeController@login'));




Route::group(array('middleware' => array('web','auth')), function () {
	Route::auth();
	Route::match(array('get', 'post'),'showRegistrationForm', array('uses' => 'Auth\AuthController@showRegistrationForm'));
	//Route::auth();
    Route::resource('items', 'ItemController');
	Route::resource('dirs', 'DirsController');
	Route::resource('users', 'UserController');
	Route::resource('transection', 'TransectionController');
	Route::resource('saleitems', 'SaleitemsController');
	Route::get('/home', 'HomeController@index');
	Route::resource('accounts', 'AccountsController');
    Route::resource('mallrokkar', 'MallrokkarController');
	Route::resource('sales', 'SalesController');
	Route::resource('mallamad', 'MallamadController');
	Route::resource('accounttype', 'AccountTypeController');
	Route::resource('back_up', 'DirsController@back_up');
	//////// reports
	Route::resource('reports', 'ReportsController');
	
	Route::any('accounts_detail/{id}', 'AccountsController@accounts_detail');
	
	Route::get('mallrokkar_get', 'MallrokkarController@account_data');
	Route::any('mallrokkar_post', 'MallrokkarController@mallrokkar_post');
	Route::any('mallrokkar_destroy/{id}', 'MallrokkarController@destroy');
	Route::any('mallrokkar_corroect/{id}', 'MallrokkarController@correct');
	
	Route::any('add_transection/', 'TransectionController@add_transection');
	Route::any('transection_destroy/{id}', 'TransectionController@destroy');
	Route::any('transection_corroect/{id}', 'TransectionController@correct');
	Route::any('sale_detail/{id}/{sale}', 'SalesController@detail');
	Route::any('item_detail/{id}', 'SalesController@item_detail');
	Route::any('closing', 'AccountsController@closing');
	Route::any('config', 'AccountsController@config');
	Route::any('sale_item_detail', 'SaleitemsController@detail');
	//Route::any('sales_detail/{id}', 'SaleitemsController@sales_detail');
	Route::any('sales_detail', 'SaleitemsController@sales_detail');
	Route::any('direct_sales', 'SalesController@direct_sales');
	Route::any('combine_sales', 'SalesController@combine_sales');
	Route::any('remove_receive_item/{id}', 'SaleitemsController@remove_receive_item');
	Route::any('saleitemsFirst', 'SaleitemsController@saleitemsFirst');
	
	Route::any('accounts_rokkar', 'AccountsController@accounts_rokkar');
	Route::any('account_detail/{id}', 'SalesController@account_detail');
	
	Route::any('mallamad_post', 'MallamadController@mallamad_post');
	Route::any('mallamad_destroy/{id}', 'MallamadController@destroy');
	Route::any('mall_amad_detail', 'MallrokkarController@mall_amad_detail');
	Route::any('mall_amad_print/{id}', 'MallamadController@mall_amad_detail');
	///////////  accounts detail ////////////
	Route::any('mall_detail', 'AccountsController@mall_detail');
	Route::any('naqdi_detail', 'AccountsController@naqdi_detail');
	Route::any('product_detail', 'AccountsController@product_detail');
	Route::any('product_detail/{id}', 'AccountsController@product_detail');
	Route::any('ser_mall_detail', 'AccountsController@ser_mall_detail');
	Route::any('ser_naqdi_detail', 'AccountsController@ser_naqdi_detail');
	
	//////// reports
	Route::resource('reports', 'ReportsController');
	//Route::any('reports/', 'ReportsController@get_items_detail');
	//Route::any('transection_destroy/{id}', 'TransectionController@destroy');
	//Route::any('transection_corroect/{id}', 'TransectionController@correct');
	
	//////////////    reports  ///////////
	//Route::any('ser_naqdi_detail/{id}', 'AccountsController@ser_naqdi_detail');
});


Route::any('receive_saleitems', 'SaleitemsController@receive_saleitems');

// Templates   
Route::group(array('prefix'=>'/templates/'),function(){
    Route::get('{template}', array( function($template)
    {
        $template = str_replace(".html","",$template);
        View::addExtension('html','php');
        return View::make('templates.'.$template);
    }));
});


//Route::get('admin', 'AdminController@get_data');  

//Route::any('transection/', 'MallrokkarController@transection');

Route::auth();

Route::get('/home', 'HomeController@index');
