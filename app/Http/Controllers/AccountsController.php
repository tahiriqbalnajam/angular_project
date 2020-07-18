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

		$account = Account::with('account_type');

		if($request->get('search')) {
			$account->where("name", "LIKE", "%{$request->get('search')}%")->orWhere("account_number", "LIKE", "%{$request->get('search')}%");
		}
		if($request->get('account_type')) {
			$account->where("account_type", "{$request->get('account_type')}");
		}

		$data['account'] = $account->paginate($data['perpage']);

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
		if($input['naam_jama'] == 'naam')
			$input['opening_balance'] = $input['opening_balance']*-1;
		
		$account = Account::create($input);
		$account  = $account->with('account_type')->first();
        return response($account, 200);
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
		$account = Account::find($id);
		$account->update($input);
		$account = $account->with('account_type')->first();
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

		$account = new Account();

		$data['account'] = $res_account = Account::find($id);
		
		/////////////////////////////////SALE DETAIL////////////////////////
		$data['sales_items_detail'] = $sales_items_detail = $account->sale_detail($id, 'naam');
		if($sales_items_detail){
			$data['sales_items_detail'] = $sales_items_detail;
		}
		else{
			$data['sales_items_detail'] = '';
		}
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = $account->sale_detail($id, 'jama');
		if($sales_items_detail_jama){
			$data['sales_items_detail_jama'] = $sales_items_detail_jama;
		}
		else{
			$data['sales_items_detail_jama'] = '';
		}

		////////////////////////////DIRECT SALE/////////////////////////
		$data['direct_sales_items_detail'] = $direct_sales_items_detail = $account->direct_sale_naam($id, 'naam');
		if($direct_sales_items_detail){
			$data['direct_sales_items_detail'] = $direct_sales_items_detail;
		}
		else{
			$data['direct_sales_items_detail'] = '';
		}

		if($id == 6){
			$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama = DB::table('transection')->select('transection.date','transection.amount')
												->where('transection.account_id',$id)
												->where('transection.type','direct_sale')
												->where('transection.payment_type','jama')
												->where('transection.deleted','no')
												->get();
												//dd(DB::getQueryLog()); 
												
				if($direct_sales_items_detail_jama){
					$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama;
				}
				else{
					$data['direct_sales_items_detail_jama'] = '';
				}
		}else{
			$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama = $account->direct_sale_jama($id);
												
				if($direct_sales_items_detail_jama){
					$data['direct_sales_items_detail_jama'] = $direct_sales_items_detail_jama;
				}
				else{
					$data['direct_sales_items_detail_jama'] = '';
				}
		}
		
		//////////////////////////////////////MAAL ROKAR///////////////////////////
		$data['mall_roakker_detail'] =$mall_roakker_detail= $account->mallrokar($id, 'naam');
		if($mall_roakker_detail){
			$data['mall_roakker_detail'] =$mall_roakker_detail;
		}
		else{$data['mall_roakker_detail'] = '' ;}
		$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama= $account->mallrokar($id, 'jama');
		if($mall_roakker_detail_jama){
			$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama;
		}
		else{
			$data['mall_roakker_detail_jama'] = '';
		}

		////////////////////////////////NAQDI ROKAR/////////////////////////////
		$data['transection_detail_naam'] = $transection_detail_naam = $account->naqdi($id, 'naam');
		if($transection_detail_naam){
			$data['transection_detail_naam'] = $transection_detail_naam;
		}
		else{
			$data['transection_detail_naam'] = '';
		}
        $data['transection_detail_jama'] = $transection_detail_jama = $account->naqdi($id, 'jama');
		if($transection_detail_jama){
			$data['transection_detail_jama'] = $transection_detail_jama;
		}
		else{
			$data['transection_detail_jama'] = '';
		}
		
		////////////////////////////////RECEIVED ITEMS//////////////////////////////
		$data['transection_detail_receive_naam'] = $transection_detail_receive_naam = $account->received_items($id, 'naam');
		if($transection_detail_receive_naam){
			$data['transection_detail_receive_naam'] = $transection_detail_receive_naam;
		}
		else{
			$data['transection_detail_receive_naam'] = '';
		}
		$data['transection_detail_receive_jama'] =$transection_detail_receive_jama = $account->received_items($id, 'jama');
		if($transection_detail_receive_jama){
			$data['transection_detail_receive_jama'] =$transection_detail_receive_jama;
		}
		else{
			$data['transection_detail_receive_jama'] = '';
		}
		$total_mall = 0;
		foreach($mall_roakker_detail as $mall){
			$total_mall += $mall->amount;
		}
		$data['total_mall'] = $total_mall;
		$total_mall_jama = 0;
		foreach($mall_roakker_detail_jama as $mall){
			$total_mall_jama += $mall->amount;
		}
		$data['total_mall_jama'] = $total_mall_jama;
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
		
		
		
		$total_jama = ($total_mall_jama + $total_naqdi_jama + $total_receive_jama + $total_sale_jama + $total_dsale_jama + $res_account->opening_balance);
		$total_naam = ($total_naqdi_naam + $total_receive_naam + $total_sale_naam + $total_dsale_naam + $total_mall);
		$balance = ($total_jama - $total_naam);
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).' NAAM';
		}
		else{
			$data['balance'] = round($balance,2).' JAMA';
		}
		//$data['balance'] = $balance;
		return response($data);
	}
	
	
	
	
	function config(Request $request)
	{
		$input = $request->all();

        DB::table('config')->where("id",'1')->update($input);
		echo 'asfd';
		//return response($data);
	}
	
	function accounts_rokkar(Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = '';
			$end_date = '';
		}
		if($start_date){
		$data['naam_jama_detail'] = $naam_jama_detail = DB::select( DB ::raw("select a.name, a.opening_balance,ts.naam_amount,ts.jama_amount,ifnull(ts.ts_balance,0)+ifnull(a.opening_balance,0)as balance from accounts a left join (select account_id, sum(case when payment_type = 'naam' then amount else 0 end) as naam_amount, sum(case when payment_type = 'jama' then amount else 0 end) as jama_amount, sum(case when payment_type = 'naam' then -amount when payment_type = 'jama' then amount else 0 end) as ts_balance from transection t where t.deleted = 'no' and (t.date between '$start_date' and '$end_date') group by t.account_id) as ts on a.id = ts.account_id "));
		}
		else{
			$data['naam_jama_detail'] = $naam_jama_detail = DB::select( DB ::raw("select a.name, a.opening_balance,ts.naam_amount,ts.jama_amount,ifnull(ts.ts_balance,0)+ifnull(a.opening_balance,0)as balance from accounts a left join (select account_id, sum(case when payment_type = 'naam' then amount else 0 end) as naam_amount, sum(case when payment_type = 'jama' then amount else 0 end) as jama_amount, sum(case when payment_type = 'naam' then -amount when payment_type = 'jama' then amount else 0 end) as ts_balance from transection t where t.deleted = 'no' group by t.account_id) as ts on a.id = ts.account_id "));
		}
		$total_naam = 0;
		$total_jama = 0;
		$total_balance = 0;
		$jama = 0;
		$naam = 0;
		foreach($naam_jama_detail as $row){
			$total_naam += $row->naam_amount;
			$total_jama += $row->jama_amount;
			$total_balance +=$row->balance;
			if($row->balance >=0){
				$jama += $row->balance;
			}
			else{
					$naam += $row->balance;
				}
		}
			$data['total_naam'] = $total_naam;
			$data['total_jama'] = $total_jama;
			//$data['total_balance'] = $total_jama-$total_naam;
			$data['total_balance'] = $total_balance;
			$data['naam'] = $naam;
			$data['jama'] = $jama;
		return response($data);
	
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
    public function mall_detail(Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = Session::get('todayDate');
			$end_date = Session::get('todayDate');
		}
		$id = $request->get('id');
		
		$data['account'] = $res_account = Account::find($id);
					
	
				
		$data['mall_roakker_detail'] =$mall_roakker_detail= DB::table('transection')->where('account_id',$id)->where('transection.type','mall_rokkar')
										->where('transection.payment_type','naam')
										->where('m.deleted','no')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('items as i', 'i.id', '=', 'm.item_id')
										->get(array('m.seller_id','transection.amount','m.rate','m.date','m.weight','m.arhat','m.date','i.title','a.name'));
		if($mall_roakker_detail){
			$data['mall_roakker_detail'] =$mall_roakker_detail;
		}
		else{$data['mall_roakker_detail'] = '' ;}
		$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama= DB::table('transection')->where('account_id',$id)->where('transection.type','mall_rokkar')
										->where('transection.payment_type','jama')
										->where('m.deleted','no')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('items as i', 'i.id', '=', 'm.item_id')
										->get(array('m.seller_id','transection.amount','m.rate','m.date','m.weight','m.arhat','m.chong_amount','m.date','i.title','a.name'));
										//return response(array('id',DB::getQueryLog()));
		if($mall_roakker_detail_jama){
			$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama;
		}
		else{
			$data['mall_roakker_detail_jama'] = '';
		}
		
		
    
		$total_mall = 0;
		foreach($mall_roakker_detail as $mall){
			$total_mall += $mall->amount;
		}
		$data['total_mall'] = $total_mall;
		$total_mall_jama = 0;
		foreach($mall_roakker_detail_jama as $mall){
			$total_mall_jama += $mall->amount;
		}
		$data['total_mall_jama'] = $total_mall_jama;
	
		
						
		$balance = (($total_mall_jama ) - ($total_mall ));
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).'  نام  ';
		}
		else{
			$data['balance'] = round($balance,2).' جمع ';
		}
		//$data['balance'] = $balance;
		return response($data);
	
	}
	function naqdi_detail(Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = Session::get('todayDate');
			$end_date = Session::get('todayDate');
		}
		$id = $request->get('id');		
		
		$data['account'] = $res_account = Account::find($id);
					
		$data['sales_items_detail'] = $sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										//->where('sales.sale_type','sale')
										->where('transection.type','sale')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id','left')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail){
			$data['sales_items_detail'] = $sales_items_detail;
		}
		else{
			$data['sales_items_detail'] = '';
		}
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										//->where('sales.sale_type','return')
										->where('transection.type','sale')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										//->get(array('sales.date','transection.amount','a.name'));
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail_jama){
			$data['sales_items_detail_jama'] = $sales_items_detail_jama;
		}
		else{
			$data['sales_items_detail_jama'] = '';
		}				
						
        $data['transection_detail_jama'] = $transection_detail_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($transection_detail_jama){
			$data['transection_detail_jama'] = $transection_detail_jama;
		}
		else{
			$data['transection_detail_jama'] = '';
		}
		$data['transection_detail_naam'] = $transection_detail_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($transection_detail_naam){
			$data['transection_detail_naam'] = $transection_detail_naam;
		}
		else{
			$data['transection_detail_naam'] = '';
		}
		
		///////////// mall rokker receive item ////////////
		$data['transection_detail_receive_naam'] = $transection_detail_receive_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_naam){
			$data['transection_detail_receive_naam'] = $transection_detail_receive_naam;
		}
		else{
			$data['transection_detail_receive_naam'] = '';
		}
		$data['transection_detail_receive_jama'] =$transection_detail_receive_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_jama){
			$data['transection_detail_receive_jama'] =$transection_detail_receive_jama;
		}
		else{
			$data['transection_detail_receive_jama'] = '';
		}
		
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
		//////////////end mall rokker receive items///////////////
		
		
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
		
		$total_sale_naam = 0;
		foreach($sales_items_detail as $sale){
			$total_sale_naam = $total_sale_naam +(($sale->price*$sale->quantity)+(($sale->price*$sale->quantity)*($sale->intrest/100)));
		}
		$data['total_sale_naam'] = $total_sale_naam;
		$total_sale_jama = 0;
		foreach($sales_items_detail_jama as $sale_j){
			$total_sale_jama = $total_sale_jama+(($sale_j->price*$sale_j->quantity)+(($sale_j->price*$sale_j->quantity)*($sale_j->intrest/100)));
		}
		$data['total_sale_jama'] = $total_sale_jama;			
		
		
		
		$total_jama = ( $total_naqdi_jama  + $total_sale_jama + $total_receive_jama );
		$total_naam = ($total_naqdi_naam  + $total_sale_naam + $total_receive_naam);
		$balance = ($total_jama - $total_naam);
		$balance = $res_account['opening_balance'] + $balance;
		//echo $res_account['opening_balance'].'[]';
		//echo $res_account->opening_balance.'->';
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).' نام ';
		}
		else{
			$data['balance'] = round($balance,2).' جمع ';
		}
		//$data['balance'] = $balance;
		return response($data);
	
	}
	
	function product_detail( Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = Session::get('todayDate');
			$end_date = Session::get('todayDate');
		}
		$id = $request->get('id');
		$data['account'] = $res_account = Account::find($id);
					
		$data['sales_items_detail'] = $sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										//->where('sales.sale_type','sale')
										->where('sa.category',$id)
										->where('transection.type','sale')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id','left')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail){
			$data['sales_items_detail'] = $sales_items_detail;
		}
		else{
			$data['sales_items_detail'] = '';
		}
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = DB::table('transection')
										->where('transection.account_id',$id)
										->where('sales.deleted','no')
										->where('sa.category',$id)
										->where('transection.type','sale')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										//->get(array('sales.date','transection.amount','a.name'));
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id','left')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail_jama){$data['sales_items_detail_jama'] = $sales_items_detail_jama;}
		else{$data['sales_items_detail_jama'] = '';}
		
		$total_sale_naam = 0;
		foreach($sales_items_detail as $sale){
			$total_sale_naam = $total_sale_naam +(($sale->price*$sale->quantity)+(($sale->price*$sale->quantity)*($sale->intrest/100)));
		}
		$data['total_sale_naam'] = $total_sale_naam;
		$total_sale_jama = 0;
		foreach($sales_items_detail_jama as $sale_j){
			$total_sale_jama = $total_sale_jama+(($sale_j->price*$sale_j->quantity)+(($sale_j->price*$sale_j->quantity)*($sale_j->intrest/100)));
		}
		$data['total_sale_jama'] = $total_sale_jama;
		///////////// mall rokker receive item ////////////
		$data['transection_detail_receive_naam'] = $transection_detail_receive_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_naam){
			$data['transection_detail_receive_naam'] = $transection_detail_receive_naam;
		}
		else{
			$data['transection_detail_receive_naam'] = '';
		}
		$data['transection_detail_receive_jama'] =$transection_detail_receive_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_jama){
			$data['transection_detail_receive_jama'] =$transection_detail_receive_jama;
		}
		else{
			$data['transection_detail_receive_jama'] = '';
		}
		$data['pro_transection_detail_jama'] = $pro_transection_detail_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($pro_transection_detail_jama){
			$data['pro_transection_detail_jama'] = $pro_transection_detail_jama;
		}
		else{
			$data['pro_transection_detail_jama'] = '';
		}
		$data['pro_transection_detail_naam'] = $pro_transection_detail_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($pro_transection_detail_naam){
			$data['pro_transection_detail_naam'] = $pro_transection_detail_naam;
		}
		else{
			$data['pro_transection_detail_naam'] = '';
		}
		$pro_total_naqdi_naam = 0;
		foreach($pro_transection_detail_naam as $naqdi_naam){
			$pro_total_naqdi_naam += $naqdi_naam->amount;
		}
		$data['pro_total_naqdi_naam'] = $pro_total_naqdi_naam;
		$pro_total_naqdi_jama = 0;
		foreach($pro_transection_detail_jama as $naqdi_jama){
			$pro_total_naqdi_jama += $naqdi_jama->amount;
		}
		$data['pro_total_naqdi_jama'] = $pro_total_naqdi_jama;		
		
		
		
		
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
		//////////////end mall rokker receive items///////////////
		$total_jama = ( $total_receive_jama  + $total_sale_jama +$pro_total_naqdi_jama );
		$total_naam = ($total_receive_naam  + $total_sale_naam + $pro_total_naqdi_naam);
		$balance = ($total_jama - $total_naam);
		$balance = $res_account->opening_balance + $balance;
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).' نام ';
		}
		else{
			$data['balance'] = round($balance,2).' جمع ';
		}
		//$data['balance'] = $balance;
		return response($data);
	}
	
	
	
	////////// service detail //////////
	public function ser_mall_detail(Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = Session::get('todayDate');
			$end_date = Session::get('todayDate');
		}
		$id = $request->get('id');
		
		$data['account'] = $res_account = Account::find($id);
					
	
				
		$data['mall_roakker_detail'] =$mall_roakker_detail= DB::table('transection')->where('account_id',$id)->where('transection.type','mall_rokkar')
										->where('transection.payment_type','naam')
										->where('m.deleted','no')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('items as i', 'i.id', '=', 'm.item_id')
										->get(array('m.seller_id','transection.amount','m.rate','m.date','m.weight','m.arhat','m.date','i.title','a.name'));
		if($mall_roakker_detail){
			$data['mall_roakker_detail'] =$mall_roakker_detail;
		}
		else{$data['mall_roakker_detail'] = '' ;}
		$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama= DB::table('transection')->where('account_id',$id)->where('transection.type','mall_rokkar')
										->where('transection.payment_type','jama')
										->where('m.deleted','no')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('items as i', 'i.id', '=', 'm.item_id')
										->get(array('m.seller_id','transection.amount','m.rate','m.date','m.weight','m.arhat','m.chong_amount','m.date','i.title','a.name'));
										//return response(array('id',DB::getQueryLog()));
		if($mall_roakker_detail_jama){
			$data['mall_roakker_detail_jama'] =$mall_roakker_detail_jama;
		}
		else{
			$data['mall_roakker_detail_jama'] = '';
		}
		
		
    
		$total_mall = 0;
		foreach($mall_roakker_detail as $mall){
			$total_mall += $mall->amount;
		}
		$data['total_mall'] = $total_mall;
		$total_mall_jama = 0;
		foreach($mall_roakker_detail_jama as $mall){
			$total_mall_jama += $mall->amount;
		}
		$data['total_mall_jama'] = $total_mall_jama;
	
		
						
		$balance = (($total_mall_jama ) - ($total_mall ));
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).'  نام  ';
		}
		else{
			$data['balance'] = round($balance,2).' جمع ';
		}
		//$data['balance'] = $balance;
		return response($data);
	
	}
	function ser_naqdi_detail(Request $request)
	{
		$date = $request->get('date');
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);			
		}
		else{
			$start_date = Session::get('todayDate');
			$end_date = Session::get('todayDate');
		}
		$id = $request->get('id');
		
		$data['account'] = $res_account = Account::find($id);
					
		$data['sales_items_detail'] = $sales_items_detail = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										//->where('sales.sale_type','sale')
										->where('transection.type','sale')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id','left')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail){
			$data['sales_items_detail'] = $sales_items_detail;
		}
		else{
			$data['sales_items_detail'] = '';
		}
		$data['sales_items_detail_jama'] = $sales_items_detail_jama = DB::table('transection')->where('transection.account_id',$id)
										->where('sales.deleted','no')
										//->where('sales.sale_type','return')
										->where('transection.type','sale')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										//->get(array('sales.date','transection.amount','a.name'));
										->join('sales_items as si', 'si.sale_id', '=', 'sales.id')
										->join('sale_items as sa', 'sa.id', '=', 'si.item_id')
										->get(array('sales.date','transection.amount','a.name','sa.name as sale_item_name','si.price','si.purchase_price','si.intrest','si.quantity','si.payment_type'));
		if($sales_items_detail_jama){
			$data['sales_items_detail_jama'] = $sales_items_detail_jama;
		}
		else{
			$data['sales_items_detail_jama'] = '';
		}				
						
        $data['transection_detail_jama'] = $transection_detail_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($transection_detail_jama){
			$data['transection_detail_jama'] = $transection_detail_jama;
		}
		else{
			$data['transection_detail_jama'] = '';
		}
		$data['transection_detail_naam'] = $transection_detail_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name'));
		if($transection_detail_naam){
			$data['transection_detail_naam'] = $transection_detail_naam;
		}
		else{
			$data['transection_detail_naam'] = '';
		}
		
		///////////// mall rokker receive item ////////////
		$data['transection_detail_receive_naam'] = $transection_detail_receive_naam = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','naam')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_naam){
			$data['transection_detail_receive_naam'] = $transection_detail_receive_naam;
		}
		else{
			$data['transection_detail_receive_naam'] = '';
		}
		$data['transection_detail_receive_jama'] =$transection_detail_receive_jama = DB::table('transection')->where('account_id',$id)
										->where('transection.deleted','no')
										->where('transection.type','receive_item')
										->where('transection.payment_type','jama')
										->whereBetween('transection.date', array($start_date, $end_date))
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
										->join('sale_items as s', 's.id', '=', 'sr.item_id')
										->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity','sr.off_loading'));
		if($transection_detail_receive_jama){
			$data['transection_detail_receive_jama'] =$transection_detail_receive_jama;
		}
		else{
			$data['transection_detail_receive_jama'] = '';
		}
		
		$total_receive_naam = 0;
		foreach($transection_detail_receive_naam as $r_naam){
			$total_receive_naam = $total_receive_naam + ($r_naam->amount);
		}
		$data['total_receive_naam']= $total_receive_naam;
		$total_receive_jama = 0;
		foreach($transection_detail_receive_jama as $r_jama){
			$total_receive_jama = $total_receive_jama + ($r_jama->amount);
		}
		$data['total_receive_jama'] = $total_receive_jama;
		//////////////end mall rokker receive items///////////////
		
		
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
		
		$total_sale_naam = 0;
		foreach($sales_items_detail as $sale){
			$total_sale_naam = $total_sale_naam +$sale->amount;
		}
		$data['total_sale_naam'] = $total_sale_naam;
		$total_sale_jama = 0;
		foreach($sales_items_detail_jama as $sale_j){
			$total_sale_jama = $total_sale_jama+$sale_j->amount;
		}
		$data['total_sale_jama'] = $total_sale_jama;			
		
		
		
		$total_jama = ( $total_naqdi_jama  + $total_sale_jama + $total_receive_jama );
		$total_naam = ($total_naqdi_naam  + $total_sale_naam + $total_receive_naam);
		$balance = ($total_jama - $total_naam);
		$balance = ($res_account->opening_balance + $balance);
		if($balance < 0){
			$data['balance'] = (round($balance,2)*-1).' نام ';
		}
		else{
			$data['balance'] = round($balance,2).' جمع ';
		}
		//$data['balance'] = $balance;
		return response($data);
	
	}
	/////////  end service detail /////
	function date_change($date)
	{
		$new_date = explode('/',$date);
		return $new_date[2].'-'.$new_date[1].'-'.$new_date[0];
	}
	
}
