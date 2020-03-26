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
    return view('phone');
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
Route::get('home/', array('uses' => 'HomeController@index'));


Route::get('KeyForm', array('uses' => 'PhoneController@key_form'));
Route::get('PhoneForm', array('uses' => 'PhoneController@get_phone_form'))->name('phone');
Route::post('save_phone', array('uses' => 'PhoneController@save_phone'));
Route::post('key_enter', array('uses' => 'PhoneController@key_enter'));
Route::get('logout/dashboard', array('uses' => 'PhoneController@logout'));

// route to process the form
//Route::post('login', array('uses' => 'HomeController@doLogin'));


	
Route::post('login', array('uses' => 'HomeController@login'));




Route::group(array('middleware' => array('web','auth')), function () {
	Route::auth();
	Route::match(array('get', 'post'),'showRegistrationForm', array('uses' => 'Auth\AuthController@showRegistrationForm'));
	//Route::auth();
    Route::resource('items', 'ItemController');
	Route::resource('users', 'UserController');
	Route::resource('transection', 'TransectionController');
	Route::resource('saleitems', 'SaleitemsController');
	Route::get('/home', 'HomeController@index');
	Route::resource('accounts', 'AccountsController');
    Route::resource('mallrokkar', 'MallrokkarController');
	Route::resource('sales', 'SalesController');
	Route::resource('mallamad', 'MallamadController');
	Route::resource('accounttype', 'AccountTypeController');
	
	Route::any('accounts_detail/{id}', 'AccountsController@accounts_detail');
	
	Route::get('mallrokkar_get', 'MallrokkarController@account_data');
	Route::any('mallrokkar_post', 'MallrokkarController@mallrokkar_post');
	Route::any('mallrokkar_destroy/{id}', 'MallrokkarController@destroy');
	Route::any('mallrokkar_corroect/{id}', 'MallrokkarController@correct');
	
	Route::any('add_transection/', 'TransectionController@add_transection');
	Route::any('transection_destroy/{id}', 'TransectionController@destroy');
	Route::any('transection_corroect/{id}', 'TransectionController@correct');
	Route::any('sale_detail/{id}/{sale}', 'SalesController@detail');
	Route::any('closing', 'AccountsController@closing');
	Route::any('config', 'AccountsController@config');
	Route::any('sale_item_detail/{id}', 'SaleitemsController@detail');
	Route::any('direct_sales', 'SalesController@direct_sales');
	Route::any('remove_receive_item/{id}', 'SaleitemsController@remove_receive_item');
	
	Route::any('accounts_rokkar', 'AccountsController@accounts_rokkar');
	Route::any('account_detail/{id}', 'SalesController@account_detail');
	
	Route::any('mallamad_post', 'MallamadController@mallamad_post');
	Route::any('mallamad_destroy/{id}', 'MallamadController@destroy');
	Route::any('mall_amad_detail', 'MallrokkarController@mall_amad_detail');
	Route::any('mall_amad_print/{id}', 'MallamadController@mall_amad_detail');
	///////////  accounts detail ////////////
	Route::any('mall_detail/{id}', 'AccountsController@mall_detail');
	Route::any('naqdi_detail/{id}', 'AccountsController@naqdi_detail');
	Route::any('product_detail/{id}', 'AccountsController@product_detail');
	Route::any('ser_mall_detail/{id}', 'AccountsController@ser_mall_detail');
	Route::any('ser_naqdi_detail/{id}', 'AccountsController@ser_naqdi_detail');
	
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
