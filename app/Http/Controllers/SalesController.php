<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Sales;
use App\Sale_items;
use App\Account;
use App\Item;
use DB;
use Session;

class SalesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
		$data['perpage'] = 4000;
		$data['todayDate'] = Session::get('todayDate');
		$check1 = array('1','2','3','4','5','6','7','8','10');
		//$data['formers'] = Account::whereIn("account_type", $check1)->get();
		$data['ginners'] = $data['formers'] = Account::all();
		$check = array('2','4');
		//$data['ginners'] = Account::whereIn("account_type", $check)->get();
		$data['ginners'] = Account::all();
		$data['all_items'] = Sale_items::get();
		$data['running_items'] = Item::get();
		if($request->get('date') || $request->get('selectAccount')){
			if($request->get('date')){
				$date = explode('/',$request->get('date'));
				$new_date = $date[2].'-'.$date[1].'-'.$date[0];
			}
			$query = DB::table('sales')->where('deleted','no');
				if($request->get('date')){
					$query->where('sales.date',$new_date);
				}
				if($request->get('selectAccount')){
					$query->where('sales.account_id',$request->get('selectAccount'));
				}
				$query->join('accounts', 'accounts.id', '=', 'sales.account_id');
			$data['items'] = $query->get(array('sales.*','accounts.name'));
		}
        else{
		  $data['items'] = Sales::where('date',Session::get('todayDate'))->join('accounts', 'accounts.id', '=', 'sales.account_id')->get(['sales.*','accounts.name']);        
        }
		
        return response($data);
    }
	public function detail($id,$running_sale)
	{
		if($running_sale == 'no'){
			$query = DB::table('sales_items')->where('sales_items.deleted','no')->where('sales_items.sale_id',$id);
				$query->join('sale_items', 'sale_items.id', '=', 'sales_items.item_id');
			$data['data'] = $query->get(array('sales_items.*','sale_items.name'));
			return response($data);
		}
		else{
			$query = DB::table('running_sale_items')->where('running_sale_items.deleted','no')->where('running_sale_items.sale_id',$id);
				$query->join('items', 'items.id', '=', 'running_sale_items.item_id');
			$data['data'] = $query->get(array('running_sale_items.*','running_sale_items.purchaser_percentage as intrest','running_sale_items.price as price','items.title as name'));
			return response($data);
		}
			
	}
	public function item_detail($id)
	{
		$data['data'] = DB::table('items')->select('items.title','items.price','items.id')->where('items.id',$id)->first();
		// = $query->(array());
		return response($data);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
		
		$detail_message = '';
    	$input = $request->all();		
		$former = $request->input('former');
		$former['former'];
		$account = Account::find($former['former']);
		if($former['sale_type'] == 'return'){
			
			if(isset($former['detail']))
				$detail = $former['detail'];
			else $detail = '';
			if(isset($former['check_no']))
				$check_no = $former['check_no'];
			else $check_no = '';
			$product = $request->input('product');
			
			//$data[];
			$g_total = 0;
			//$percen = 0;
			foreach($product as $pre){
				$percen = 0;
				$sale_item_detail = Sale_items::find($pre['saleitem']);
				$intrest = $pre['intrest'] ;
				if($pre['payment_type'] == 'cash'){
					$percen = 0;
					$intr = 0;
				}
				else{
					$percen =  ($pre['price']*$pre['qty'])*($pre['intrest']/100);
					$intr = $pre['intrest'] ;
				}
					$detail_message .= 'Item:'.$sale_item_detail->name.', Qty:'.$pre['qty'].', Rate:'.$pre['price'].', payment_type:'.$pre['payment_type'].', intrest:'.$intr;
				$total = $pre['price']*$pre['qty'];
				$total = $total+$percen;
				$g_total = $g_total+$total;
			}
			$sale_id = DB::table('sales')->insertGetId(
				['account_id' => $former['former'],
				'amount' => $g_total,
				'detail' => $detail,
				'check_no' => $check_no,
				'date' => Session::get('todayDate'),
				'deleted' => 'no',
				'sale_type' => 'return'
				]);
				$grand_jama = 0;
				$cash_total = 0;
				$tttlll = 0;
			foreach($product as $pre){
				if($pre['payment_type'] == 'cash'){
							$intr = 0;
							$cash_total = ($pre['price']*$pre['qty']);
						}
						else{
							$intr = $pre['intrest'] ;
							$cash_total = 0;
						}
			$tttlll += $cash_total;
			$sale_item_ = Sale_items::find($pre['saleitem']);
			$new_qty = $sale_item_['quantity'] + $pre['qty'];
			$total_jama = $sale_item_['price'] * $pre['qty'];
			DB::table('sale_items')->where('id', $pre['saleitem'])->update(['quantity' => $new_qty]);			
				DB::table('sales_items')->insert([
				['quantity' => $pre['qty'],
				'item_id' => $pre['saleitem'],
				'price' => $pre['price'],
				'purchase_price' => $sale_item_['price'],
				'sale_id' => $sale_id,
				'payment_type' => $pre['payment_type'],
				'intrest' => $intr,
				'deleted' => 'no',
				]
				]);
				
				DB::table('transection')->insert([
				['account_id' => $sale_item_['category'],
				'amount' => $total_jama,
				'type' => 'sale',
				'detail' => 'Amount From Return Sale',
				'payment_type' => 'naam',			
				'date' => Session::get('todayDate'),
				'sale_purchase_id' => $sale_id,
				]
				]);
				$grand_jama += $total_jama; 

			}
			DB::table('transection')->insert([
				['account_id' => $former['former'],
				'amount' => $g_total,
				'type' => 'sale',
				'detail' => 'Amount From Return Sale',
				'payment_type' => 'jama',			
				'date' => Session::get('todayDate'),
				'sale_purchase_id' => $sale_id,
				]
				]);
			if($tttlll != 0){
					DB::table('transection')->insert([
						['account_id' => $former['former'],
						'amount' => $tttlll,
						'type' => 'sale',
						'detail' => 'Amount From Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						]
						]);
				}
			DB::table('transection')->insert([
				['account_id' => 6,
				'amount' => $g_total - $grand_jama,
				'type' => 'sale',
				'detail' => 'Amount From Return Sale',
				'payment_type' => 'naam',			
				'date' => Session::get('todayDate'),
				'sale_purchase_id' => $sale_id,
				]
				]);
			
			//sms
			$config = DB::table('config')->first();
			$account = Account::find($former['former']);
			if($account->sms == 'yes'){
				$username =$config->sms_id;
				$password =$config->sms_pass;
				$sender = "Faizan Corp" ;
				$mobile = $account->phone;
				
				$message = str_replace('{name}',$account->name,$config->sale_sms);
				$message = str_replace('{amount}',$g_total,$message);
				$message = str_replace('{detail}',$detail_message,$message);
				$message .= ' (Sale Return)';
				
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
				$all_items = Sale_items::get();
				return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
			}
			else{
				$all_items = Sale_items::get();
				return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
			}
		
		}
		else{
			if(isset($former['check_no'])){
				if($former['check_no'] >=$account['check_from'] && $former['check_no'] <=$account['check_to']){
					if(isset($former['detail']))
						$detail = $former['detail'];
					else $detail = '';
					if(isset($former['check_no']))
						$check_no = $former['check_no'];
					else $check_no = '';
					$product = $request->input('product');
					//$data[];
					$g_total = 0;
					foreach($product as $pre){
						$percen = 0;
						$sale_item_detail = Sale_items::find($pre['saleitem']);
						$intrest = $pre['intrest'] ;
						if($pre['payment_type'] == 'cash'){
							$percen = 0;
							$intr = 0;
						}
						else{
							$percen =  ($pre['price']*$pre['qty'])*($pre['intrest']/100);
							$intr = $pre['intrest'] ;
						}
						$detail_message .= 'Item:'.$sale_item_detail->name.', Qty:'.$pre['qty'].', Rate:'.$pre['price'];
						$total = $pre['price']*$pre['qty'];
						$total = $total + $percen;
						$g_total = $g_total+$total;
					}
					$sale_id = DB::table('sales')->insertGetId(
						['account_id' => $former['former'],
						'amount' => $g_total,
						'detail' => $detail,
						'check_no' => $check_no,
						'date' => Session::get('todayDate'),
						'deleted' => 'no',
						]);
						$grand_jama = 0;
						$cash_total = 0;
						$tttlll = 0;
					foreach($product as $pre){
						if($pre['payment_type'] == 'cash'){
							$intr = 0;
							$cash_total = ($pre['price']*$pre['qty']);
						}
						else{
							$intr = $pre['intrest'] ;
							$cash_total = 0;
						}
						$tttlll += $cash_total;
						$item_id = $pre['saleitem'];				
						$sale_item_ = Sale_items::find($pre['saleitem']);
						$new_qty = $sale_item_['quantity'] - $pre['qty'];
						$total_jama = $sale_item_['price'] * $pre['qty'];
						DB::table('sale_items')->where('id', $pre['saleitem'])->update(['quantity' => $new_qty]);
						
						DB::table('sales_items')->insert([
						['quantity' => $pre['qty'],
						'item_id' => $pre['saleitem'],
						'price' => $pre['price'],
						'purchase_price' => $sale_item_['price'],
						'sale_id' => $sale_id,
						'payment_type' => $pre['payment_type'],
						'intrest' => $intr,
						'deleted' => 'no',
						]
						]);
						
						DB::table('transection')->insert([
						['account_id' => $sale_item_['category'],
						'amount' => $total_jama,
						'type' => 'sale',
						'detail' => 'Amount From Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						]
						]);
						$grand_jama += $total_jama; 
						//$data[] = $pre['price'];
					}
					DB::table('transection')->insert([
						['account_id' => $former['former'],
						'amount' => $g_total,
						'type' => 'sale',
						'detail' => 'Amount From Sale',
						'payment_type' => 'naam',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						]
						]);
					if($tttlll !=0 ){
						DB::table('transection')->insert([
							['account_id' => $former['former'],
							'amount' => $tttlll,
							'type' => 'sale',
							'detail' => 'Amount From Sale',
							'payment_type' => 'jama',			
							'date' => Session::get('todayDate'),
							'sale_purchase_id' => $sale_id,
							]
							]);
					}
					DB::table('transection')->insert([
						['account_id' => 6,
						'amount' => $g_total - $grand_jama,
						'type' => 'sale',
						'detail' => 'Amount From Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						]
						]);
					
					//sms
				$config = DB::table('config')->first();
				$account = Account::find($former['former']);
				if($account->sms == 'yes'){
					$username =$config->sms_id;
					$password =$config->sms_pass;
					$sender = "Faizan Corp" ;
					$mobile = $account->phone;
					
					$message = str_replace('{name}',$account->name,$config->sale_sms);
					$message = str_replace('{amount}',$g_total,$message);
					$message = str_replace('{detail}',$detail_message,$message);
					
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
				/*if(strpos($responce,'OK') === 0)
					$response = 'Message Sent';
				else{
					$response = $responce;
					}
						$all_items = Sale_items::get();
					return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));*/
				if(isset($responce)){
					if(strpos($responce,'OK') === 0)
						$response = 'Message Sent';
					else{
						$response = $responce;
						}
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
					}
					else{
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
					}
				}
				else{
					return response(array('check_no_error'=>'Check No is not in range'));
				}
			}
			else{
				if(isset($former['detail']))
					$detail = $former['detail'];
				else $detail = '';
				if(isset($former['check_no']))
					$check_no = $former['check_no'];
				else $check_no = '';
				$product = $request->input('product');
				//$data[];
				//print_r($request->input('product'));
				$g_total = 0;
				foreach($product as $pre){
					$percen = 0;
					$sale_item_detail = Sale_items::find($pre['saleitem']);
					$intrest = $pre['intrest'] ;
						if($pre['payment_type'] == 'cash'){
							$percen = 0;
							$intr = 0;
						}
						else{
							$percen =  ($pre['price']*$pre['qty'])*($pre['intrest']/100);
							$intr = $pre['intrest'] ;
						}
						$detail_message .= 'Item:'.$sale_item_detail->name.', Qty:'.$pre['qty'].', Rate:'.$pre['price'];
					$total = $pre['price']*$pre['qty'];
					$total = $total+$percen;
					$g_total = $g_total+$total;
				}
				$sale_id = DB::table('sales')->insertGetId(
					['account_id' => $former['former'],
					'amount' => $g_total,
					'detail' => $detail,
					'check_no' => $check_no,
					'date' => Session::get('todayDate'),
					'deleted' => 'no',
					]);
					$grand_jama = 0;
					$cash_total = 0;
					$tttlll = 0;
				foreach($product as $pre){
					if($pre['payment_type'] == 'cash'){
							$intr = 0;
							$cash_total = ($pre['price']*$pre['qty']);
						}
						else{
							$intr = $pre['intrest'] ;
							$cash_total = 0;
						}
					$tttlll += $cash_total;
					$item_id = $pre['saleitem'];				
					$sale_item_ = Sale_items::find($pre['saleitem']);
					$new_qty = $sale_item_['quantity'] - $pre['qty'];
					$total_jama = $sale_item_['price'] * $pre['qty'];
					DB::table('sale_items')->where('id', $pre['saleitem'])->update(['quantity' => $new_qty]);
					
					DB::table('sales_items')->insert([
					['quantity' => $pre['qty'],
					'item_id' => $pre['saleitem'],
					'price' => $pre['price'],
					'purchase_price' => $sale_item_['price'],
					'payment_type' => $pre['payment_type'],
					'sale_id' => $sale_id,
					'intrest' => $intr,
					'deleted' => 'no',
					]
					]);
					
					DB::table('transection')->insert([
					['account_id' => $sale_item_['category'],
					'amount' => $total_jama,
					'type' => 'sale',
					'detail' => 'Amount From Sale',
					'payment_type' => 'jama',			
					'date' => Session::get('todayDate'),
					'sale_purchase_id' => $sale_id,
					]
					]);
					$grand_jama += $total_jama; 
					//$data[] = $pre['price'];
				}
				DB::table('transection')->insert([
					['account_id' => $former['former'],
					'amount' => $g_total,
					'type' => 'sale',
					'detail' => 'Amount From Sale',
					'payment_type' => 'naam',			
					'date' => Session::get('todayDate'),
					'sale_purchase_id' => $sale_id,
					]
					]);
				if($tttlll != 0){
					DB::table('transection')->insert([
						['account_id' => $former['former'],
						'amount' => $tttlll,
						'type' => 'sale',
						'detail' => 'Amount From Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						]
						]);
				}
				DB::table('transection')->insert([
					['account_id' => 6,
					'amount' => $g_total - $grand_jama,
					'type' => 'sale',
					'detail' => 'Amount From Sale',
					'payment_type' => 'jama',			
					'date' => Session::get('todayDate'),
					'sale_purchase_id' => $sale_id,
					]
					]);
				
				//sms
				$config = DB::table('config')->first();
				$account = Account::find($former['former']);
				if($account->sms == 'yes'){
					$username =$config->sms_id;
					$password =$config->sms_pass;
					$sender = "Faizan Corp" ;
					$mobile = $account->phone;
					
					$message = str_replace('{name}',$account->name,$config->sale_sms);
					$message = str_replace('{amount}',$g_total,$message);
					$message = str_replace('{detail}',$detail_message,$message);
					
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
				/*if(strpos($responce,'OK') === 0)
					$response = 'Message Sent';
				else{
					$response = $responce;
					}
					$all_items = Sale_items::get();
				return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));*/
				if(isset($responce)){
					if(strpos($responce,'OK') === 0)
						$response = 'Message Sent';
					else{
						$response = $responce;
						}
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
					}
					else{
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
					}
			}
		}
       
    }
	public function receive_saleitems(Request $request)
	{
		$res = DB::table('sale_items')->where('id',$request->input('name'))->first();
		$qty = $res->quantity + $request->input('quantity');
		DB::table('sale_items')->where('id', $request->input('name'))->update(array('quantity' => $qty,'price'=>$request->input('price')));         
		DB::table('sale_items_received')->insert(array('quantity' => $request->input('quantity'),
			'price' => $request->input('price'),
			'supplier_id' => $request->input('supplier'),
			'date' => Session::get('todayDate'),
			'item_id' => $request->input('name')));
        return response($res->id);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        DB::table('transection')->where('sale_purchase_id', $id)->where('type', 'sale')->update(['deleted' => 'no']);
		DB::table('sales_items')->where('sale_id', $id)->update(['deleted' => 'no']);
		DB::table('sales')->where('id', $id)->update(['deleted' => 'no']);
        $sales_items = DB::select("SELECT * FROM sales_items WHERE sale_id = $id ");
		foreach($sales_items as $row){
			$item_id = $row->item_id;				
			$sale_item_ = Sale_items::find($row->item_id);
			$new_qty = $sale_item_['quantity'] - $row->quantity;
			DB::table('sale_items')->where('id', $row->item_id)->update(['quantity' => $new_qty]);
			/*$qty_result = DB::select("SELECT * FROM `sale_items_received` WHERE item_id = $item_id and quantity != qty_sold order by id ");
			$sold_qty = $row->quantity;
			foreach($qty_result as $row){
				$new_quantity = $row->quantity - $row->qty_sold;
				if($new_quantity >= $sold_qty){
					DB::table('sale_items_received')->where('id', $row->id)->update(['qty_sold' => $row->qty_sold+$sold_qty]);
					break;
				}
				else {
					DB::table('sale_items_received')->where('id', $row->id)->update(['qty_sold' => $row->qty_sold+$new_quantity]);					
					$sold_qty -= $new_quantity;
				}
			}*/
		}

		$item = Sales::where('sales.id',$id)->join('accounts', 'accounts.id', '=', 'sales.account_id')->get(['sales.*','accounts.name']);        
        return response($item[0]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
    	DB::table('transection')->where('sale_purchase_id', $id)->where('type', 'sale')->update(['deleted' => 'no']);
		DB::table('sales_items')->where('sale_id', $id)->update(['deleted' => 'no']);
		DB::table('sales')->where('id', $id)->update(['deleted' => 'no']);
		
				
        $item = Sale_items::find($id);
        return response($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
		DB::table('transection')->where('sale_purchase_id', $id)->where('type', 'sale')->update(['deleted' => 'yes']);
		DB::table('transection')->where('sale_purchase_id', $id)->where('type', 'direct_sale')->update(['deleted' => 'yes']);
		DB::table('sales_items')->where('sale_id', $id)->update(['deleted' => 'yes']);
		DB::table('sales')->where('id', $id)->update(['deleted' => 'yes']);
		$sales_items = DB::select("SELECT * FROM sales_items WHERE sale_id = $id ");
		foreach($sales_items as $row){
			$sale_item_ = Sale_items::find($row->item_id);
			$new_qty = $sale_item_['quantity'] + $row->quantity;
			DB::table('sale_items')->where('id', $row->item_id)->update(['quantity' => $new_qty]);
			/*$qty_result = DB::select("SELECT * FROM `sale_items_received` WHERE item_id = ".$row->item_id." and qty_sold != 0 order by id desc ");
			$qty_delet = $row->quantity;
			foreach($qty_result as $row1)			
			if($qty_delet <= $row1->qty_sold){
				DB::table('sale_items_received')->where('id', $row1->id)->update(['qty_sold' => $row1->qty_sold-$qty_delet]);
				break;
			}
			else{
				$qty_delet = $row->quantity-$row1->qty_sold;
				DB::table('sale_items_received')->where('id', $row1->id)->update(['qty_sold' => 0]);
			}*/
		}		
		
				
		
		$item = Sales::where('sales.id',$id)->join('accounts', 'accounts.id', '=', 'sales.account_id')->get(['sales.*','accounts.name']);        
        return response($item[0]);
    }
	public function direct_sales(Request $request)
	{
		$detail_message = '';
    	$input = $request->all();		
		$former = $request->input('former');
		$former['former'];
		$account = Account::find($former['former']);
		//$account_seller = Account::find($former['seller']);
		/*if($former['sale_type'] == 'return'){
			
			if(isset($former['detail']))
				$detail = $former['detail'];
			else $detail = '';
			if(isset($former['check_no']))
				$check_no = $former['check_no'];
			else $check_no = '';
			$product = $request->input('product');
			//$data[];
			$g_seller_total = 0;
					$g_former_total = 0;
					foreach($product as $pre){
						$sale_item_detail = Item::find($pre['saleitem']);
						$detail_message .= 'Item:'.$sale_item_detail->title.', Qty:'.$pre['qty'].', Rate:'.$pre['sale_price'];						
						//$seller_total = $pre['purchaser_total']-$pre['saler_total'];
						$g_former_total = $pre['purchaser_total']-$pre['saler_total'];
						$g_former_total = $g_former_total+$pre['total'];
					}
					$data_sales = array('account_id' => $former['former'],
						'amount' => $g_former_total,
						'detail' => $detail,
						'check_no' => $check_no,
						'date' => Session::get('todayDate'),
						'deleted' => 'no',
						'sale_type' => 'return',
						'running_sale' => 'yes',
						'rabih_kharif' => $former['rabih_kharif'],
						);
						
					DB::beginTransaction();

					try {

					$sale_id = DB::table('sales')->insertGetId($data_sales);
						$grand_jama = 0;
					foreach($product as $pre){
						$g_seller_total = $g_seller_total+$pre['saler_total'];
						DB::table('running_sale_items')->insert(array(
						'quantity' => $pre['qty'],
						'item_id' => $pre['saleitem'],
						'price' => $pre['purchase_price'],
						'sale_price' => $pre['purchaser_total'],
						'purchase_price' => $pre['saler_total'],
						'purchaser_id' => $former['former'],
						'seller_id' => $pre['seller'],
						'sale_id' => $sale_id,
						'saler_percentage' => $pre['saler'],
						'purchaser_percentage' => $pre['purchaser'],
						'deleted' => 'no',
						'date' => Session::get('todayDate'),
						));
						
						DB::table('transection')->insert(array(
						'account_id' => $pre['seller'],
						'amount' => $pre['saler_total'],
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'naam',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
						
					}
					DB::table('transection')->insert(array(
						'account_id' => $former['former'],
						'amount' => $g_former_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
					
					DB::table('transection')->insert(
						array('account_id' => 6,
						'amount' => $g_former_total - $g_seller_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'naam',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
						DB::commit();
						} catch (\Exception $e) {
				DB::rollback();
				// something went wrong
			}
			//sms
			$config = DB::table('config')->first();
			$account = Account::find($former['former']);
			if($account->sms == 'yes'){
				$username =$config->sms_id;
				$password =$config->sms_pass;
				$sender = "Faizan Corp" ;
				$mobile = $account->phone;
				
				$message = str_replace('{name}',$account->name,$config->sale_sms);
				$message = str_replace('{amount}',$seller_total,$message);
				$message = str_replace('{detail}',$detail_message,$message);
				$message .= ' (Sale Return)';
				
				$part = "http://sendpk.com/";
					$url = "http://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message);					
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
				$all_items = Sale_items::get();
				return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
			}
			else{
				$all_items = Sale_items::get();
				return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
			}
		
		}
		else{
			if(isset($former['check_no'])){
				if($former['check_no'] >=$account['check_from'] && $former['check_no'] <=$account['check_to']){
					if(isset($former['detail']))
						$detail = $former['detail'];
					else $detail = '';
					if(isset($former['check_no']))
						$check_no = $former['check_no'];
					else $check_no = '';
					$product = $request->input('product');
					//$data[];
					$g_seller_total = 0;
					$g_former_total = 0;
					foreach($product as $pre){
						$sale_item_detail = Item::find($pre['saleitem']);
						$detail_message .= 'Item:'.$sale_item_detail->title.', Qty:'.$pre['qty'].', Rate:'.$pre['sale_price'];						
						//$seller_total = $pre['total']-$pre['bachat'];
						//$g_seller_total = $g_seller_total+$seller_total;
						$g_former_total = $g_former_total+$pre['purchaser_total'];
					}
					$sale_id = DB::table('sales')->insertGetId(
						array('account_id' => $former['former'],
						'amount' => $g_former_total,
						'detail' => $detail,
						'check_no' => $check_no,
						'date' => Session::get('todayDate'),
						'deleted' => 'no',
						'running_sale' => 'yes',
						));
						$grand_jama = 0;
					foreach($product as $pre){
						$g_seller_total = $g_seller_total+$pre['saler_total'];
						DB::table('running_sale_items')->insert(array(
						'quantity' => $pre['qty'],
						'item_id' => $pre['saleitem'],
						'sale_price' => $pre['purchaser_total'],
						'purchase_price' => $pre['saler_total'],
						'purchaser_id' => $former['former'],
						'seller_id' => $pre['seller'],
						'sale_id' => $sale_id,
						'saler_percentage' => $pre['saler'],
						'purchaser_percentage' => $pre['purchaser'],
						'deleted' => 'no',
						'date' => Session::get('todayDate'),
						));
						
						DB::table('transection')->insert(array(
						'account_id' => $pre['seller'],
						'amount' => $pre['saler_total'],
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
						
					}
					DB::table('transection')->insert(array(
						'account_id' => $former['former'],
						'amount' => $g_former_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'naam',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
					
					DB::table('transection')->insert(
						array('account_id' => 6,
						'amount' => $g_former_total - $g_seller_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
					
					//sms
				$config = DB::table('config')->first();
				$account = Account::find($former['former']);
				if($account->sms == 'yes'){
					$username =$config->sms_id;
					$password =$config->sms_pass;
					$sender = "Faizan Corp" ;
					$mobile = $account->phone;
					
					$message = str_replace('{name}',$account->name,$config->sale_sms);
					$message = str_replace('{amount}',$seller_total,$message);
					$message = str_replace('{detail}',$detail_message,$message);
					
					$part = "http://sendpk.com/";
						$url = "http://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message);						
						$ch = curl_init();
						$timeout = 300;
						curl_setopt($ch,CURLOPT_URL,$url);
						
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
						curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
						$responce = curl_exec($ch);
						curl_close($ch); 
				}
				
				if(isset($responce)){
					if(strpos($responce,'OK') === 0)
						$response = 'Message Sent';
					else{
						$response = $responce;
						}
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
					}
					else{
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
					}
				}
				else{
					return response(array('check_no_error'=>'Check No is not in range'));
				}
			//}
			//else{*/
				if(isset($former['detail']))
					$detail = $former['detail'];
				else $detail = '';
				if(isset($former['check_no']))
					$check_no = $former['check_no'];
				else $check_no = '';
				$product = $request->input('product');
				//$data[];
				$g_seller_total = 0;
					$g_former_total = 0;
					foreach($product as $pre){
						$sale_item_detail = Item::find($pre['saleitem']);
						$detail_message .= 'Item:'.$sale_item_detail->title.', Qty:'.$pre['qty'].', Rate:'.$pre['sale_price'];						
						//$seller_total = $pre['total']-$pre['bachat'];
						//$g_seller_total = $g_seller_total+$seller_total;
						$g_former_total = $g_former_total+$pre['purchaser_total'];
					}
					
					DB::beginTransaction();

					try {
					
					$sale_id = DB::table('sales')->insertGetId(
						array('account_id' => $former['former'],
						'amount' => $g_former_total,
						'detail' => $detail,
						'check_no' => $check_no,
						'date' => Session::get('todayDate'),
						'deleted' => 'no',
						'running_sale' => 'yes',
						'rabih_kharif' => $former['rabih_kharif'],
						));
						$grand_jama = 0;
					foreach($product as $pre){
						$g_seller_total = $g_seller_total+$pre['saler_total'];
						DB::table('running_sale_items')->insert(array(
						'quantity' => $pre['qty'],
						'item_id' => $pre['saleitem'],
						'price' => $pre['purchase_price'],
						'sale_price' => $pre['purchaser_total'],
						'purchase_price' => $pre['saler_total'],
						'purchaser_id' => $former['former'],
						'seller_id' => $pre['seller'],
						'sale_id' => $sale_id,
						'saler_percentage' => $pre['saler'],
						'purchaser_percentage' => $pre['purchaser'],
						'deleted' => 'no',
						'date' => Session::get('todayDate'),
						));
						
						DB::table('transection')->insert(array(
						'account_id' => $pre['seller'],
						'amount' => $pre['saler_total'],
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
						
					}
					DB::table('transection')->insert(array(
						'account_id' => $former['former'],
						'amount' => $g_former_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'naam',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
					
					DB::table('transection')->insert(
						array('account_id' => 6,
						'amount' => $g_former_total - $g_seller_total,
						'type' => 'direct_sale',
						'detail' => 'Amount From Direct Sale',
						'payment_type' => 'jama',			
						'date' => Session::get('todayDate'),
						'sale_purchase_id' => $sale_id,
						));
						DB::commit();
						} catch (\Exception $e) {
						DB::rollback();
						// something went wrong
					}
				
				//sms
				$config = DB::table('config')->first();
				$account = Account::find($former['former']);
				if($account->sms == 'yes'){
					$username =$config->sms_id;
					$password =$config->sms_pass;
					$sender = "Faizan Corp" ;
					$mobile = $account->phone;
					
					$message = str_replace('{name}',$account->name,$config->sale_sms);
					$message = str_replace('{amount}',$seller_total,$message);
					$message = str_replace('{detail}',$detail_message,$message);
					
					$part = "http://sendpk.com/";
						$url = "http://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message);						
						$ch = curl_init();
						$timeout = 300;
						curl_setopt($ch,CURLOPT_URL,$url);
						
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
						curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
						$responce = curl_exec($ch);
						curl_close($ch); 
				}
				if(isset($responce)){
					if(strpos($responce,'OK') === 0)
						$response = 'Message Sent';
					else{
						$response = $responce;
						}
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>$response));
					}
					else{
						$all_items = Sale_items::get();
						return response(array('id'=>$sale_id,'all_items'=>$all_items,'detail'=>$detail_message,'res'=>'Messages are not active'));
					}
			//}
		}
       
    
	//}
	
	
	function account_detail($id)
	{
		$data['naam_jama_detail'] = $naam_jama_detail = DB::select( DB ::raw("select sum(case when payment_type = 'naam' then amount else 0 end) as naam_amount,sum(case when payment_type = 'jama' then amount else 0 end) as jama_amount,a.name , sum(case when payment_type = 'naam' then -amount when payment_type = 'jama' then amount else 0 end) as balance from transection t join accounts a on a.id = t.account_id where t.deleted = 'no' and t.account_id = $id group by t.account_id"));
		$total_naam = 0;
		$total_jama = 0;
		$total_balance = 0;
		foreach($naam_jama_detail as $row){
			$total_naam += $row->naam_amount;
			$total_jama += $row->jama_amount;
			}
			$data['total_naam'] = $total_naam;
			$data['total_jama'] = $total_jama;
			$balance = $total_jama-$total_naam;
			if($balance<0){
				$data['total_balance'] = round(($balance*-1),2).' نام';
			}
			else{
				$data['total_balance'] = round($balance,2).' جمع';
			}
		return response($data);
	
	}
}
