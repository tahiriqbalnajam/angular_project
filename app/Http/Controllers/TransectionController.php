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

class TransectionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	public function index(Request $request)
	{
		$data['amountInHand'] = Session::get('amountInHand');
		$data['todayDate'] = Session::get('todayDate');
        $input = $request->all();
		$data['all_accounts'] = Account::get();
		//print_r($input);
		/*$check = [2,3];
		$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
		$data['seller_account'] = Account::where("account_type", "1")->get();*/
		if($request->get('date') || $request->get('selectAccount')){
			if($request->get('selectAccount')){
					$data['amountInHand'] = 0;
				}
			if($request->get('date')){
				$date = explode('/',$request->get('date'));
				$new_date = $date[2].'-'.$date[1].'-'.$date[0];
				
				$amountInHand = DB::table('amount_in_hand')->where('date',$new_date)->first(array('amount'));
				$data['amountInHand'] = $amountInHand->amount;
			}
			$query = DB::table('transection')->where('first','no')
				->where('payment_type','jama')->whereNotIn( 'transection.type', array('mall_rokkar'));
				if($request->get('date')){
					$query->where('transection.date',$new_date);
				}
				if($request->get('selectAccount')){
					$query->where('transection.account_id',$request->get('selectAccount'));
				}
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
			$data['transection_jama'] = $query->get(array('transection.*','a.name'));
			
			$query1 = DB::table('transection')->where('first','no')
				->where('payment_type','naam')->whereNotIn( 'transection.type', array('mall_rokkar'));
				if($request->get('date')){
					$query1->where('transection.date',$new_date);
				}
				if($request->get('selectAccount')){
					$query1->where('transection.account_id',$request->get('selectAccount'));
				}
				$query1->join('accounts as a', 'a.id', '=', 'transection.account_id');
				$data['transection_naam'] = $query1->get(array('transection.*','a.name'));
			
			/*$check = [2,3];
			$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
			$data['seller_account'] = Account::where("account_type", "1")->get();
			
			$data['items'] = Item::get();
			$data['saler_rokkar'] = Mallrokkar::where('mallroakker.date',$new_date)
			->join('accounts as a', 'a.id', '=', 'mallroakker.seller_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')			  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','a.name as seller','i.title as title']);
			$data['purchaser_rokkar'] = Mallrokkar::where('mallroakker.date',$new_date)
			->join('accounts as b', 'b.id', '=', 'mallroakker.purchaser_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')		  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','b.name as purchaser','i.title as title']);*/
		}		
		else{
			$query = DB::table('transection')
				->where('payment_type','jama')
				->where('first','no')
				->where('date',Session::get('todayDate'))
				->whereNotIn( 'transection.type', array('mall_rokkar'));
				$query->join('accounts as a', 'a.id', '=', 'transection.account_id');
			$data['transection_jama'] = $query->get(array('transection.*','a.name'));
			$data['transection_naam'] = DB::table('transection')->where('first','no')->where('payment_type','naam')->where('date',Session::get('todayDate'))->whereNotIn( 'transection.type', array('mall_rokkar'))->join('accounts as a', 'a.id', '=', 'transection.account_id')->get(array('transection.*','a.name'));			
			/*$check = [2,3];
			$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
			$data['seller_account'] = Account::where("account_type", "1")->get();
			$data['items'] = Item::get();
			$data['saler_rokkar'] = Mallrokkar::where('mallroakker.date',date('Y-m-d'))
			->join('accounts as a', 'a.id', '=', 'mallroakker.seller_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')			  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','a.name as seller','i.title as title']);
			$data['purchaser_rokkar'] = Mallrokkar::where('mallroakker.date',date('Y-m-d'))
			->join('accounts as b', 'b.id', '=', 'mallroakker.purchaser_id')
			->join('items as i', 'i.id', '=', 'mallroakker.item_id')		  	
		  	->orderBy('mallroakker.id', 'desc')
		  	->paginate(5000,['mallroakker.*','b.name as purchaser','i.title as title']);*/
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
		$id = DB::table('mallroakker')->insertGetId(
			['seller_id' => $request->input('saler_account'),
			'purchaser_id' => $request->input('purchaser_account'),
			'seller_amount' => $request->input('s_grand_total'),
			'purchaser_amount' => $request->input('p_grand_total'),
			'total_amount' => $request->input('total'),
			'chong_amount' => $request->input('chong'),
			'rate' => $request->input('rate'),
			'weight' => $request->input('weight'),
			'arhat' => $request->input('arhat'),
			'item_id' => $request->input('item_id'),
			'date' => Session::get('todayDate'),
			]
			);
			DB::table('transection')->insert([
			['account_id' => $request->input('purchaser_account'),
			'amount' => $request->input('p_grand_total'),
			'mallroaker_id' => $id,
			'payment_type' => 'naam',			
			'date' => date('Y-m-d')
			]
			]);
			DB::table('transection')->insert([
			['account_id' => $request->input('saler_account'),
			'amount' => $request->input('s_grand_total'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]
			]);	
			DB::table('transection')->insert([
			['account_id' => 2,
			'amount' => $request->input('palydari'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '3',
			'amount' => $request->input('brokri'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '4',
			'amount' => $request->input('anjuman_fund'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '5',
			'amount' => $request->input('petty_cash'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '1',
			'amount' => $request->input('arhat'),
			'mallroaker_id' => $id,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
		//die('asdfsdafsad');
		//dd('asdf');
		
		//print_r($request);
		return response($request);
    	/*$input = $request->all();
        $create = Account::create($input);
		$create1 = Account::select('accounts.id AS id', 'accounts.*','account_types.id  as typeid','account_types.title')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$create->id)->first();
        return response($create1);*/
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
    public function update(Request $request,$id)
    {
    	$input = $request->all();

        Account::where("id",$id)->update($input);
        //$account = Account::find($id);
		$account = Account::select('accounts.id as id','accounts.name','accounts.address','account_types.title')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$id)->first();
        return response($account);
    }


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
		DB::table('transection')->where('id', $id)->update(['deleted' => 'yes']);
		
		//Mallrokkar::where("id",$id)->update(['deleted' => 'yes']);
		return $id;
        //return Account::where('id',$id)->delete();
    }
	
	public function correct($id)
    {
		DB::table('transection')->where('id', $id)->update(['deleted' => 'no']);
		
		//Mallrokkar::where("id",$id)->update(['deleted' => 'no']);
		return $id;
        //return Account::where('id',$id)->delete();
    }
	
	public function account_data()
	{
		$check = [2,3];
		$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
		$data['seller_account'] = Account::where("account_type", "1")->get();
		$data['items'] = Item::get();
		return response($data);
	}
	
	public function add_transection( Request $request)
	{
		//echo $request->input('amount');
		if($request->input('detail'))
			$detail = $request->input('detail');
		else $detail = '';
		$id = DB::table('transection')->insertGetId(
			['account_id' => $request->input('account_jama'),
			'amount' => $request->input('amount'),
			'detail' => $detail,
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]);
			
		$id = DB::table('transection')->insertGetId(
			['account_id' => $request->input('account_naam'),
			'amount' => $request->input('amount'),
			'detail' => $detail,
			'payment_type' => 'naam',			
			'date' => Session::get('todayDate')
			]);
			
			
			//sms
			$config = DB::table('config')->first();
			$account = Account::find($request->input('account_jama'));
			if($account->sms == 'yes'){
				$username =$config->sms_id;
				$password =$config->sms_pass;
				$sender = "Faizan Corp" ;
				$mobile = $account->phone;
				
				$message = str_replace('{name}',$account->name,$config->transection_sms);
				$message = str_replace('{amount}',$request->input('amount'),$message);
				$message = str_replace('{payment_type}',$request->input('payment_type'),$message);
				$message = str_replace('{detail}',$request->input('detail'),$message);
				
				$part = "http://sendpk.com/";
					$url = "http://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message);
					//$url = $part."api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message)." ";
					//die();
					$ch = curl_init();
					$timeout = 3000;
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
				return response(array('id'=>$id,'res'=>$response));
			}
			else{
				return response(array('id'=>$id,'res'=>'Messages are not active'));
			}
	}
	public function transection()
	{
		
	}
}
