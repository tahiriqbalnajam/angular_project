<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Account;
use App\Account_type;
use DB;
use Session;

class AccountsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
		//print_r($input);
		$data['perpage'] = 4000;
		$data['config'] = DB::table('config')->first();
		$data['account_type'] = Account_type::all();
        if($request->get('search') && $request->get('account_type')){
            $data['account'] = Account::where("name", "LIKE", "%{$request->get('search')}%")->orWhere("account_number", "LIKE", "%{$request->get('search')}%")
				->where("account_type", "{$request->get('account_type')}")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate($data['perpage'],['accounts.id AS id', 'accounts.*','account_types.id as typeid','account_types.title']);     
        }
		else if($request->get('search') && $request->get('account_type') == ''){
            $data['account'] = Account::where("name", "LIKE", "%{$request->get('search')}%")->orWhere("account_number", "LIKE", "%{$request->get('search')}%")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate($data['perpage'],['accounts.id AS id', 'accounts.*','account_types.id as typeid','account_types.title']);     
        }
		else if($request->get('account_type') && $request->get('search') == ''){
			$acnt_type = ($request->get('account_type'))?$request->get('account_type'):'';
            $data['account'] = Account::where("account_type","{$request->get('account_type')}")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate($data['perpage'],['accounts.id AS id', 'accounts.*','account_types.id as typeid','account_types.title']);     
        }
		else{
		  $data['account'] = Account::join('account_types', 'accounts.account_type', '=', 'account_types.id')->orderBy('accounts.id', 'desc')
		  	->paginate($data['perpage'],['accounts.id AS id', 'accounts.*','account_types.id as typeid','account_types.title']);
		  	//->get();
        }
		//echo $input['account_type'];
        return response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	 
    public function store(Request $request)
    {
		
    	$input = $request->all();
        $create = Account::create($input);
		$create1 = Account::select('accounts.id AS id', 'accounts.*','account_types.id  as typeid','account_types.title')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$create->id)->first();
        return response($create1);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $account = Account::find($id);
        return response($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
    	$input = $request->all();

        Account::where("id",$id)->update($input);
        //$account = Account::find($id);
		$account = Account::select('accounts.id as id','accounts.name','accounts.address','account_types.title','accounts.check_from','accounts.check_to')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$id)->first();
        return response($account);
    }
	public function closing(Request $request)
	{
		//echo $request->input('date');
		//echo Session::get('todayDate');
		$date = explode('/',$request->get('date'));
			$new_date = $date[2].'-'.$date[1].'-'.$date[0];
		$res = DB::table('closing_date')->latest('id')->first();
		$amount = DB::table('amount_in_hand')->where('date',Session::get('todayDate'))->first();
		echo $jama = DB::table('transection')->where('payment_type','jama')->where('deleted','no')->where('date',Session::get('todayDate'))->whereNotIn( 'transection.type', array('receive_item','mall_rokkar'))->sum('transection.amount');		
		echo $naam = DB::table('transection')->where('payment_type','naam')->where('deleted','no')->where('date',Session::get('todayDate'))->whereNotIn( 'transection.type', array('receive_item','mall_rokkar'))->sum('transection.amount');
		echo $amount_in_hand = (((int)$amount->amount+(int)$jama) - (int)$naam);
		DB::table('amount_in_hand')->insert(array(
			'amount' => $amount_in_hand,						
			'date' => $new_date
			));
		DB::table('closing_date')->insert(array(						
			'date' => $new_date
			));
		Session::set('todayDate', $new_date);
		Session::set('amountInHand', $amount_in_hand);
		echo 'yes';	
		//return response(array('di'=>'yes'));
	}
	public function accounts_detail($id)
	{
		$data['account'] = $res_account = Account::find($id);
		if($res_account->type == 'product' || $res_account->account_type == '6'){
			$data['nnn'] = $nnn = 'yes';
			$data['sales_items_detail'] = $sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','sale')
										->where('transection.type','sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.name as item_name','transection.amount','si.quantity','a.name'));
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','return')
										->where('transection.type','sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.name as item_name','transection.amount','si.quantity','a.name'));
		$data['direct_sales_items_detail'] = $direct_sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','sale')
										->where('transection.type','direct_sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('running_sale_items as si', 'si.sale_id', '=', 'sales.id')
										->join('items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.title as item_name','transection.amount','si.quantity','a.name'));
		$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','return')
										->where('transection.type','direct_sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('running_sale_items as si', 'si.sale_id', '=', 'sales.id')
										->join('items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.title as item_name','transection.amount','si.quantity','a.name'));
 		}
		else {
			$data['nnn'] = $nnn = 'not';
		$data['sales_items_detail'] = $sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','sale')
										->where('transection.type','sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.name as item_name','si.price','si.quantity','a.name'));
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','return')
										->where('transection.type','sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.name as item_name','si.price','si.quantity','a.name'));
		$data['direct_sales_items_detail'] = $direct_sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','sale')
										->where('transection.type','direct_sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('running_sale_items as si', 'si.sale_id', '=', 'sales.id')
										->join('items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.title as item_name','si.sale_price as price','si.quantity','a.name'));
		$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','return')
										->where('transection.type','direct_sale')
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('running_sale_items as si', 'si.sale_id', '=', 'sales.id')
										->join('items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.title as item_name','si.sale_price as price','si.quantity','a.name'));
		}
		/*$data['sales_items_detail'] = DB::table('sales')->where('sales.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','sale')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','sa.name as item_name','si.price','si.quantity','a.name'));
		$data['sales_items_detail_jama'] = DB::table('sales')->where('sales.account_id',$id)
										->where('sales.deleted','no')
										->where('sales.sale_type','return')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										//->join('transection as t', 'sales.id', '=', 't.sale_purchase_id')
										->get(array('sales.date','sa.name as item_name','si.price','si.quantity','a.name'));*/
		$data['mall_roakker_detail'] =$mall_roakker_detail= DB::table('mallroakker')->where('seller_id',$id)
										->where('mallroakker.deleted','no')
										->join('accounts as a', 'a.id', '=', 'mallroakker.seller_id')
										->join('items as i', 'i.id', '=', 'mallroakker.item_id')
										->get(array('mallroakker.seller_id','mallroakker.seller_amount','mallroakker.rate','mallroakker.weight','mallroakker.arhat','mallroakker.date','i.title','a.name'));
        $data['transection_detail_jama'] = $transection_detail_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','jama')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		$data['transection_detail_naam'] = $transection_detail_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','naam')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		$data['transection_detail_receive_naam'] = $transection_detail_receive_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','naam')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity'));
		$data['transection_detail_receive_jama'] =$transection_detail_receive_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','jama')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity'));
		$total_mall = 0;
		foreach($mall_roakker_detail as $mall){
			$total_mall += $mall->seller_amount;
		}
		$data['total_mall'] = $total_mall;
		$total_naqdi_naam = 0;
		foreach($transection_detail_naam as $naqdi_naam){
			$total_naqdi_naam += $naqdi_naam->amount;
		}
		$data['total_naqdi_naam'] = $total_naqdi_naam;
		$total_naqdi_jama = 0;
		foreach($transection_detail_jama as $naqdi_jama){
			$total_naqdi_jama += $naqdi_jama->amount;
		}
		$data['total_naqdi_jama'] = $total_naqdi_jama;
		$total_receive_naam = 0;
		foreach($transection_detail_receive_naam as $r_naam){
			$total_receive_naam = $total_receive_naam + ($r_naam->price*$r_naam->quantity);
		}
		$data['total_receive_naam']= $total_receive_naam;
		$total_receive_jama = 0;
		foreach($transection_detail_receive_jama as $r_jama){
			$total_receive_jama = $total_receive_jama + ($r_jama->price*$r_jama->quantity);
		}
		$data['total_receive_jama'] = $total_receive_jama;
		if($nnn == 'yes'){
			$total_sale_naam = 0;
			foreach($sales_items_detail as $sale){
				$total_sale_naam = $total_sale_naam +($sale->amount);
			}
			$data['total_sale_naam'] = $total_sale_naam;
			$total_sale_jama = 0;
			foreach($sales_items_detail_jama as $sale_j){
				$total_sale_jama = $total_sale_jama+($sale_j->amount);
			}
			$data['total_sale_jama'] = $total_sale_jama;
			$total_dsale_naam = 0;
			if(isset($direct_sales_items_detail)){
				foreach($direct_sales_items_detail as $dsale){
					$total_dsale_naam = $total_dsale_naam + $dsale->amount;
				}
			}
			$data['total_dsale_naam'] = $total_dsale_naam;
			$total_dsale_jama = 0;
			if(isset($direct_sales_items_detail_jama)){
				foreach($direct_sales_items_detail_jama as $dsale_j){
					$total_dsale_jama = $total_dsale_jama + $dsale_j->amount;
				}
			}
			$data['total_dsale_jama'] = $total_dsale_jama;
		}
		else{
			$total_sale_naam = 0;
			foreach($sales_items_detail as $sale){
				$total_sale_naam = $total_sale_naam +($sale->price * $sale->quantity);
			}
			$data['total_sale_naam'] = $total_sale_naam;
			$total_sale_jama = 0;
			foreach($sales_items_detail_jama as $sale_j){
				$total_sale_jama = $total_sale_jama+($sale_j->price * $sale_j->quantity);
			}
			$data['total_sale_jama'] = $total_sale_jama;
			$total_dsale_naam = 0;
			if(isset($direct_sales_items_detail)){
				foreach($direct_sales_items_detail as $dsale){
					$total_dsale_naam = $total_dsale_naam + ($dsale->price * $dsale->quantity);
				}
			}
			$data['total_dsale_naam'] = $total_dsale_naam;
			$total_dsale_jama = 0;
			if(isset($direct_sales_items_detail_jama)){
				foreach($direct_sales_items_detail_jama as $dsale_j){
					$total_dsale_jama = $total_dsale_jama + ($dsale_j->price * $dsale_j->quantity);
				}
			}
			$data['total_dsale_jama'] = $total_dsale_jama;
		}
		
		$total_jama = ($total_mall + $total_naqdi_jama + $total_receive_jama + $total_sale_jama + $total_dsale_jama + $res_account->opening_balance);
		$total_naam = $total_naqdi_naam + $total_receive_naam + $total_sale_naam + $total_dsale_naam;
		$balance = $total_jama - $total_naam;
		if($balance < 0){
			$data['balance'] = ($balance*-1).' NAAM';
		}
		else{
			$data['balance'] = $balance.' JAMA';
		}
		return response($data);
	}
	function config(Request $request)
	{
		$input = $request->all();

        DB::table('config')->where("id",'1')->update($input);
		echo 'asfd';
		//return response($data);
	}


	/*public function show()
	{
		36794323504780{"di":"yes"}
		
		$account = Account::paginate(5);		
        return response($account);
	}*/
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    
	
}
