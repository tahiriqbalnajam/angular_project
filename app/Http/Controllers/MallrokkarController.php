<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Account;
use App\Item;
use App\Mallrokkar;
use DB;
use Session;

class MallrokkarController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
		$data['todayDate'] = Session::get('todayDate');
        $input = $request->all();
		//print_r($input);
		$check = [1,2,3,4,5,7,8,10];
		$check1 = [1,8];
		//$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
		//$data['seller_account'] = Account::whereIn("account_type", $check1)->get();
		$data['purchaser_account'] = $data['seller_account'] = Account::all();
		$data['all_account'] = Account::get();
		if($request->get('date') || $request->get('percha_no') || $request->get('account')){
			$percha_no = $request->get('percha_no');
			$account_id = $request->get('account');
			if($request->get('date') != 'undefined'){
			$date = explode('/',$request->get('date'));
			$new_date = $date[2].'-'.$date[1].'-'.$date[0];
			}
			else{$new_date = Session::get('todayDate');}
			$check = [2,3];
			//$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
			//$data['seller_account'] = Account::where("account_type", "1")->get();
			
			$data['items'] = Item::get();
			/*$data['saler_rokkar'] = Mallrokkar::where('mallroakker.date',$new_date)
			->join('accounts as a', 'a.id', '=', 'mallroakker.seller_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')			  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','a.name as seller','i.title as title']);
			$data['purchaser_rokkar'] = Mallrokkar::where('mallroakker.date',$new_date)
			->join('accounts as b', 'b.id', '=', 'mallroakker.purchaser_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')		  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','b.name as purchaser','i.title as title']);*/
			//DB::enableQueryLog();
			$query = DB::table('transection')
				->where('payment_type','jama');
				if($percha_no != 'undefined')
					$query->where('m.percha_no',$percha_no);
				if($account_id != 'undefined')
					$query->where('transection.account_id',$account_id);
				if($new_date != 'undefined')
					$query->where('transection.date',$new_date);
					
				$query->where( 'transection.type', 'mall_rokkar');
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
				$query->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id');
			$data['saler_rokkar'] = $query->get(array('transection.*','a.name','m.rate','m.weight'));
			//dd(DB::getQueryLog()); 
			 $query= DB::table('transection')->where('payment_type','naam');
										if($percha_no != 'undefined')
											$query->where('m.percha_no',$percha_no);
										if($account_id != 'undefined')
											$query->where('transection.account_id',$account_id);
										if($new_date != 'undefined')
											$query->where('transection.date',$new_date);
											
										$data['purchaser_rokkar']  = $query->where( 'transection.type', 'mall_rokkar')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')
										->get(array('transection.*','a.name','m.rate','m.weight'));
										//received items
			/*$query = DB::table('transection')->where('payment_type','jama')
				->where('transection.date',$new_date)
				->where( 'transection.type', 'receive_item');
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
				$query->join('sale_items_received as si', 'si.id', '=', 'transection.sale_purchase_id');
			$data['receive_jama'] = $query->get(array('transection.*','a.name','si.quantity','si.price'));
			$data['receive_naam'] = DB::table('transection')->where('payment_type','naam')
										->where('transection.date',$new_date)
										->where( 'transection.type', 'receive_item')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as si', 'si.id', '=', 'transection.sale_purchase_id')
										->get(array('transection.*','a.name','si.quantity','si.price'));			*/
			
		}		
		else{
			$check = [1,2,3,4,5,7,8,10];
			$check1 = [1,8];
			//$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
			//$data['seller_account'] = Account::whereIn("account_type", $check1)->get();
			$data['items'] = Item::get();
			/*$data['saler_rokkar'] = Mallrokkar::where('mallroakker.date',date('Y-m-d'))
			->join('accounts as a', 'a.id', '=', 'mallroakker.seller_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')			  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','a.name as seller','i.title as title']);
			$data['purchaser_rokkar'] = Mallrokkar::where('mallroakker.date',date('Y-m-d'))
			->join('accounts as b', 'b.id', '=', 'mallroakker.purchaser_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')		  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','b.name as purchaser','i.title as title']);*/			
			$query = DB::table('transection')->where('payment_type','jama')
				->where('transection.date',Session::get('todayDate'))
				->where( 'transection.type', 'mall_rokkar');
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
				$query->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id');
			$data['saler_rokkar'] = $query->get(array('transection.*','a.name','m.rate','m.weight'));
			$data['purchaser_rokkar'] = DB::table('transection')->where('payment_type','naam')
										->where('transection.date',Session::get('todayDate'))
										->where( 'transection.type', 'mall_rokkar')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('mallroakker as m', 'm.id', '=', 'transection.sale_purchase_id')										
										->get(array('transection.*','a.name','m.rate','m.weight'));
										//received items
			/*$query = DB::table('transection')->where('payment_type','jama')
				->where('transection.date',Session::get('todayDate'))
				->where( 'transection.type', 'receive_item');
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
				$query->join('sale_items_received as si', 'si.id', '=', 'transection.sale_purchase_id');
			$data['receive_jama'] = $query->get(array('transection.*','a.name','si.quantity','si.price'));
			$data['receive_naam'] = DB::table('transection')->where('payment_type','naam')
										->where('transection.date',Session::get('todayDate'))
										->where( 'transection.type', 'receive_item')
										->join('accounts as a', 'a.id', '=', 'transection.account_id')
										->join('sale_items_received as si', 'si.id', '=', 'transection.sale_purchase_id')
										->get(array('transection.*','a.name','si.quantity','si.price'));			*/
			
        }		
        return response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {		
		// enter data in mallroakker tabel
		$seller = $request->input('seller');
		$id = DB::table('mallroakker')->insertGetId(
			array('seller_id' => $seller['saler_account'],
			'purchaser_id' => '',
			'seller_amount' => $seller['s_grand_total'],
			'purchaser_amount' => $seller['p_grand_total'],
			'total_amount' => $seller['total'],
			'chong_amount' => $seller['chong'],
			'rate' => $seller['rate'],
			'weight' => $seller['weight'],
			'arhat' => $seller['arhat'],
			'item_id' => $seller['item_id'],
			'percha_no' => $seller['percha_no'],
			'date' => Session::get('todayDate'),
			));
			$purchasers = $request->input('purchaser');
			//foreach($request->input('purchaser') as $seller){
				$purchaser_total = 0;
				$amount = 0;
				foreach($purchasers as $purchaser){
					$amount = ($purchaser['price']*($purchaser['weight']/40))+(($purchaser['price']*($purchaser['weight']/40))*.016);
					$purchaser_total += $amount;
				DB::table('transection')->insert(array(
				'account_id' => $purchaser['purchaser_account'],
				'amount' => $amount,
				'payment_type' => 'naam',
				'sale_purchase_id' => $id,
				'type' => 'mall_rokkar',			
				'date' => Session::get('todayDate')
				));
			}
			if($seller['ttl'] < $purchaser_total){
				DB::table('transection')->insert(array(
				'account_id' => 6,
				'amount' => $purchaser_total - $seller['ttl'],
				'sale_purchase_id' => $id,
				'type' => 'mall_rokkar',
				'payment_type' => 'jama',			
				'date' => Session::get('todayDate')
				));
			}
			
			DB::table('transection')->insert(array(
			'account_id' => $seller['saler_account'],
			'amount' => $seller['s_grand_total'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			));	
			DB::table('transection')->insert(array(
			'account_id' => 2,
			'amount' => $seller['palydari'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			));
			DB::table('transection')->insert(array(
			'account_id' => '3',
			'amount' => $seller['brokri'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			));
			DB::table('transection')->insert(array(
			'account_id' => '4',
			'amount' => $seller['anjuman_fund'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			));
			DB::table('transection')->insert(array(
			'account_id' => '5',
			'amount' => $seller['petty_cash'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			));
			DB::table('transection')->insert(array(
			'account_id' => '1',
			'amount' => $seller['arhat'],
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			));
			
			//sms
			$config = DB::table('config')->first();
			$account = Account::find($seller['saler_account']);
			if($account->sms == 'yes'){
				$username ="923451075051" ;
				$password ="5718" ;
				$sender = "Faizan Corp" ;
				$mobile = $account->phone;
				
				$message = str_replace('{name}',$account->name,$config->mall_roakker_sms);
				$message = str_replace('{rate}',$seller['rate'],$message);
				$message = str_replace('{weight}',$seller['weight'],$message);
				$message = str_replace('{total}',$seller['s_grand_total'],$message);
				//$message = "This is Test";$request->input('s_grand_total')
				
				$part = "http://sendpk.com/";
					$url = "http://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message);
					//$url = $part."api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message)." ";
					//die();
					$ch = curl_init();
					$timeout = 300;
					curl_setopt($ch,CURLOPT_URL,$url);
					
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
					$responce = curl_exec($ch);
					curl_close($ch); 
			}
			///Write out the response
			//echo $response;
			//end sms
			if(isset($responce)){
				if(strpos($responce,'OK') === 0)
					$response = 'Message Sent';
				else{
					$response = $responce;
					}	
				return response(array('id'=>$id,'res'=> $responce));
			}
			else
				return response(array('id'=>$id,'res'=>'Messages are not active'));
		
    }
	public function mallrokkar_post(Request $request)
	{
				
		// enter data in mallroakker tabel
		$id = DB::table('mallroakker')->insertGetId(
			['seller_id' => $request->input('saler_account'),
			'purchaser_id' => $request->input('purchaser_account'),
			'seller_amount' => $request->input('p_grand_total'),
			'purchaser_amount' => $request->input('p_grand_total'),
			'total_amount' => $request->input('total'),
			'chong_amount' => $request->input('chong'),
			'rate' => '0',
			'weight' => $request->input('weight'),
			'arhat' => '0',
			'item_id' => $request->input('item_id'),
			'percha_no' => $request->input('percha_no'),
			'date' => Session::get('todayDate'),
			]
			);
			DB::table('transection')->insert([
			['account_id' => $request->input('purchaser_account'),
			'amount' => $request->input('p_grand_total'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'naam',			
			'date' => Session::get('todayDate')
			]
			]);
			DB::table('transection')->insert([
			['account_id' => $request->input('saler_account'),
			'amount' => $request->input('p_grand_total'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]
			]);
			
			
					
		return response(array('id'=>$id));    	
    
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $account = Mallrokkar::find($id);
        return response($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
   /* public function update(Request $request,$id)
    {
    	$input = $request->all();

        Account::where("id",$id)->update($input);
        //$account = Account::find($id);
		$account = Account::select('accounts.id as id','accounts.name','accounts.address','account_types.title')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$id)->first();
        return response($account);
    }*/


	/*public function show()
	{
		$account = Account::paginate(5);		
        return response($account);
	}*/
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
		DB::table('transection')->where('sale_purchase_id', $id)->where('type','mall_rokkar')->update(['deleted' => 'yes']);
		
		Mallrokkar::where("id",$id)->update(['deleted' => 'yes']);
		return $id;
        //return Account::where('id',$id)->delete();
    }
	
	public function correct($id)
    {
		DB::table('transection')->where('sale_purchase_id', $id)->where('type','mall_rokkar')->update(['deleted' => 'no']);
		
		Mallrokkar::where("id",$id)->update(['deleted' => 'no']);
		return $id;
        //return Account::where('id',$id)->delete();
    }
	
	/*public function account_data()
	{
		$check = [2,3];
		$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
		$data['seller_account'] = Account::where("account_type", "1")->get();
		$data['items'] = Item::get();
		return response($data);
	}*/
	
	/*public function add_transection( Request $request)
	{
		//echo $request->input('amount');
		DB::table('transection')->insert([
			['account_id' => $request->input('account'),
			'amount' => $request->input('amount'),
			'detail' => $request->input('detail'),
			'payment_type' => $request->input('payment_type'),			
			'date' => date('Y-m-d'),
			]
			]);
			//echo $request->input('amount');
	}
	public function transection()
	{
		
	}*/
	
	
	
	
	
	
	
}
