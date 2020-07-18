<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Account extends Model
{

    public $fillable = ['name','address','phone','cnic','account_type','status','account_number','account_number_old','check_from','check_to','type','sms','opening_balance'];            

	public function get_account_type()
	{
		return Account_type::all();
	}

	public function account_type() {
    	return $this->belongsTo('App\Account_type', 'account_type');
	}
	public function sales() {
    	return $this->hasMany('App\Sales');
	}

	public function transaction() {
    	return $this->hasMany('App\Transaction');
	}

	public function sale_detail($accountid = '', $type = 'naam') {
		if($accountid)
			return DB::table('transection')->where('transection.account_id',$accountid)
										->where('sales.deleted','no')
										->where('transection.type','sale')
										->where('transection.payment_type',$type)
										->join('sales', 'sales.id', '=', 'transection.sale_purchase_id')
										->join('accounts as a', 'a.id', '=', 'sales.account_id')
										->get(array('sales.date','transection.amount','a.name'));
		else
			return false;
	}

	public function direct_sale_naam($id = '') {
		if($id)
			return  DB::table('transection')->where('transection.account_id',$id)
			->where('running_sale_items.deleted','no')
			->where('transection.type','direct_sale')
			->where('transection.deleted','no')
			->where('transection.payment_type', 'naam')
			->join('running_sale_items', 'running_sale_items.sale_id', '=', 'transection.sale_purchase_id')
			->join('items as i', 'i.id', '=', 'running_sale_items.item_id')
			->groupBy('running_sale_items.id')
			->get(array('running_sale_items.date','running_sale_items.price','running_sale_items.sale_price as amount','running_sale_items.quantity','running_sale_items.purchaser_percentage','i.title'));
		else
			return false;
	}

	public function direct_sale_jama($id = '') {
		if($id)
			return DB::table('transection')->select('running_sale_items.date','running_sale_items.price','running_sale_items.purchase_price as amount','running_sale_items.quantity','running_sale_items.saler_percentage','i.title')
			->where('transection.account_id',$id)
			->where('running_sale_items.deleted','no')
			//->where('sales.sale_type','sale')
			->where('transection.type','direct_sale')
			->where('transection.deleted','no')
			->where('transection.payment_type','jama')
			->join('running_sale_items', function ($join){
				$join->on('running_sale_items.sale_id', '=', 'transection.sale_purchase_id');
				$join->on('running_sale_items.seller_id', '=', 'transection.account_id');
			})
			
			//->join('running_sale_items', 'running_sale_items.seller_id', '=', 'transection.account_id')
			->join('items as i', 'i.id', '=', 'running_sale_items.item_id')
			//->join('running_sale_items as si', 'si.sale_id', '=', 'sales.id')
			//->join('items as sa', 'sa.id', '=', 'si.item_id')
			->groupBy('running_sale_items.id')
			->get();
			//dd(DB::getQueryLog()); 
		else
			return false;
	}

	public function mallrokar($id = '', $type = 'naam') {
		if($id)
			return DB::table('transection')->where('account_id',$id)->where('transection.type','mall_rokkar')
			->where('transection.payment_type', $type)
			->where('m.deleted','no')
			->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
			->join('accounts as a', 'a.id', '=', 'transection.account_id')
			->join('items as i', 'i.id', '=', 'm.item_id')
			->get(array('m.seller_id','transection.amount','m.rate','m.weight','m.arhat','m.chong_amount','m.date','i.title','a.name'));
		else
			return false;
	}

	

	public function naqdi($id = '',  $type = 'naam') {
		if($id)
			return DB::table('transection')->where('account_id',$id)
			->where('transection.deleted','no')
			->where('transection.type','')
			->where('transection.payment_type', $type)
			->join('accounts as a', 'a.id', '=', 'transection.account_id')
			->get(array('transection.amount','transection.detail','transection.date','a.name'));
		else
			return false;
	}
	public function received_items($id = '',  $type = 'naam') {
		if($id)
			return DB::table('transection')->where('account_id',$id)
			->where('transection.deleted','no')
			->where('transection.type','receive_item')
			->where('transection.payment_type', $type)
			->join('accounts as a', 'a.id', '=', 'transection.account_id')
			->join('sale_items_received as sr', 'sr.id', '=', 'transection.sale_purchase_id')
			->join('sale_items as s', 's.id', '=', 'sr.item_id')
			->get(array('transection.amount','transection.detail','transection.date','a.name','s.name as item_name','sr.price','sr.quantity'));
		else
			return false;
	}
	public function naqdi2($id = '',  $type = 'naam') {
		if($id)
			return '';
		else
			return false;
	}
	public function naqdi3($id = '',  $type = 'naam') {
		if($id)
			return '';
		else
			return false;
	}
	public function naqdi4($id = '',  $type = 'naam') {
		if($id)
			return '';
		else
			return false;
	}
}
