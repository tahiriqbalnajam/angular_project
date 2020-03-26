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

class MallamadController extends Controller
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
		$check = [2,3];
		$check1 = [1,8];
		$data['purchaser_account'] = Account::whereIn("account_type", $check)->get();
		$data['seller_account'] = Account::whereIn("account_type", $check1)->get();
		$data['all_account'] = Account::get();
		$data['items'] = Item::get();
		if($request->get('date')){			
			$date = explode('/',$request->get('date'));
			$new_date = $date[2].'-'.$date[1].'-'.$date[0];
			$data['mall_amad_detail'] =$mall_amad_detail= DB::table('mall_amad')
										->where('mall_amad.deleted','no')
										->where('mall_amad.date',$new_date)
										->join('accounts as a', 'a.id', '=', 'mall_amad.account_id')
										->join('items as i', 'i.id', '=', 'mall_amad.item_id')
										->get(array('mall_amad.*','i.title','a.name'));			
					
			
		}		
		else{
			$data['mall_amad_detail'] =$mall_amad_detail= DB::table('mall_amad')
										->where('mall_amad.deleted','no')
										->join('accounts as a', 'a.id', '=', 'mall_amad.account_id')
										->join('items as i', 'i.id', '=', 'mall_amad.item_id')
										->get(array('mall_amad.*','i.title','a.name'));
										
			
			
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
			'percha_no' => $request->input('percha_no'),
			'date' => Session::get('todayDate'),
			]
			);
			DB::table('transection')->insert([
			['account_id' => $request->input('purchaser_account'),
			'amount' => $request->input('p_grand_total'),			
			'payment_type' => 'naam',
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',			
			'date' => Session::get('todayDate')
			]
			]);
			DB::table('transection')->insert([
			['account_id' => $request->input('saler_account'),
			'amount' => $request->input('s_grand_total'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]
			]);	
			DB::table('transection')->insert([
			['account_id' => 2,
			'amount' => $request->input('palydari'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate')
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '3',
			'amount' => $request->input('brokri'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '4',
			'amount' => $request->input('anjuman_fund'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '5',
			'amount' => $request->input('petty_cash'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			DB::table('transection')->insert([
			['account_id' => '1',
			'amount' => $request->input('arhat'),
			'sale_purchase_id' => $id,
			'type' => 'mall_rokkar',
			'payment_type' => 'jama',			
			'date' => Session::get('todayDate'),
			]
			]);
			
			//sms
			$config = DB::table('config')->first();
			$account = Account::find($request->input('saler_account'));
			if($account->sms == 'yes'){
				$username ="923451075051" ;
				$password ="5718" ;
				$sender = "Faizan Corp" ;
				$mobile = $account->phone;
				
				$message = str_replace('{name}',$account->name,$config->mall_roakker_sms);
				$message = str_replace('{rate}',$request->input('rate'),$message);
				$message = str_replace('{weight}',$request->input('weight'),$message);
				$message = str_replace('{total}',$request->input('s_grand_total'),$message);
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
		DB::table('mall_amad')->where('id', $id)->update(['deleted' => 'yes']);
		
		//Mallrokkar::where("id",$id)->update(['deleted' => 'yes']);
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
	
	//////////// mall amad //////////
	public function mallamad_post(Request $request)
	{
		$date = explode('/',$request->input('date'));
		$new_date = $date[2].'-'.$date[1].'-'.$date[0];
		$id = DB::table('mall_amad')->insertGetId(
			array(
				'account_id' => $request->input('saler_account'),
				'item_id' => $request->input('item_id'),
				'weight' => $request->input('weight'),
				'date' => $new_date)
				);
		$mall_amad_detail= DB::table('mall_amad')
										->where('mall_amad.deleted','no')
										->where('mall_amad.id',$id)
										->join('accounts as a', 'a.id', '=', 'mall_amad.account_id')
										->join('items as i', 'i.id', '=', 'mall_amad.item_id')
										->get(array('mall_amad.*','i.title','a.name'));
		$data['mall_amad_detail'] =$mall_amad_detail[0];
		$data['id'] = $id;
		echo $id;
		//return response($data);
	}
	
	function mall_amad_detail($id)
	{
		//$data['account'] = $res_account = Account::find($id);
					
	
				
//		$data['mall_amad_detail'] =$mall_amad_detail= DB::table('mall_amad')->where('account_id',$id)->where('transection.type','mall_rokkar')
		$data['config'] = DB::table('config')->first();
		$mall_amad_detail= DB::table('mall_amad')
										->where('mall_amad.id',$id)
										->where('mall_amad.deleted','no')
										->join('accounts as a', 'a.id', '=', 'mall_amad.account_id')
										->join('items as i', 'i.id', '=', 'mall_amad.item_id')
										->get(array('mall_amad.*','i.title','a.name'));
										
			$data['mall_amad_detail'] =$mall_amad_detail[0];
			return response($data);
	}
	
	///////////// end mall amad //////
	
	
	
	
	
}
