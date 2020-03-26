<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Sale_items;
use App\Account;
use DB;
use Session;

class SaleitemsController extends Controller
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
		//$data['supplier'] = Account::whereIn("account_type", array(1, 2, 3,4,5,6,7,8,10))->get();
		$data['supplier'] = Account::all();
		$data['category'] = Account::where("type", "product")->get();
		$data['todayDate'] = Session::get('todayDate');
		$data['all_items'] = Sale_items::get();
        if($request->get('search')){
            $data['items'] = Sale_items::where("Sale_items.name", "LIKE", "%{$request->get('search')}%")
									->orWhere("a.name", "LIKE", "%{$request->get('search')}%")
									->join('sale_items_received as si', 'Sale_items.id', '=', 'si.item_id')
		  								->join('accounts as a', 'a.id', '=', 'si.supplier_id')
										->groupBy('si.item_id')
										->paginate($data['perpage'],['Sale_items.*','a.name as supplier']);
        }else{
		  $data['items'] = Sale_items::join('sale_items_received as si', 'Sale_items.id', '=', 'si.item_id')
		  								->join('accounts as a', 'a.id', '=', 'si.supplier_id')
										->groupBy('si.item_id')
										->paginate($data['perpage'],['Sale_items.*','a.name as supplier']);
		  								//->paginate($data['perpage']);
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
    	$input = $request->all();
        //$create = Sale_items::create($input);
		$create_id = DB::table('Sale_items')->insertGetId(array(
			'name' => $request->input('name'),
			'quantity' => $request->input('quantity'),
			'price' => ($request->input('price')+$request->input('off_loading')),
			'category' => $request->input('category')
			));
		$data['all_items'] = Sale_items::get();
		
		$id = DB::table('sale_items_received')->insertGetId(array('quantity' => $request->input('quantity'),
			'price' => $request->input('price'),
			'supplier_id' => $request->input('supplier'),
			'off_loading' => $request->input('off_loading'),
			'date' => Session::get('todayDate'),
			'item_id' => $create_id));
			
		DB::table('transection')->insert(array('amount' => $request->input('quantity')*$request->input('price'),
			'payment_type' => 'jama',
			'sale_purchase_id' => $id,
			'type' => 'receive_item',
			'detail' => 'Sale Items',
			'account_id' => $request->input('supplier'),
			'date' => Session::get('todayDate')));
		
		DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('price')),
			'payment_type' => 'naam',
			'sale_purchase_id' => $id,
			'type' => 'receive_item',
			'detail' => 'Sale Items',
			'account_id' => $request->input('category'),
			'date' => Session::get('todayDate')));
		DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('off_loading')),
				'payment_type' => 'naam',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'سیل آئیٹم ان لوڈنگ خرچہ۔',
				'account_id' => 6,
				'date' => Session::get('todayDate')));
			
		$data['create'] = Sale_items::where("Sale_items.id", $create_id)
			->join('sale_items_received as si', 'Sale_items.id', '=', 'si.item_id')
				->join('accounts as a', 'a.id', '=', 'si.supplier_id')
				->first(array('Sale_items.*','a.name as supplier'));
        return response($data);
    }
	public function receive_saleitems(Request $request)
	{
		$res = DB::table('sale_items')->where('id',$request->input('name'))->first();
		if($request->input('type') == 'purchase'){			
			$qty = $res->quantity + $request->input('quantity');
			$old_price = $res->quantity * $res->price;
			//$off_loading = $request->input('off_loading');
			//$off_loading_per_piece = $request->input('off_loading') / $request->input('quantity');
			$new_price = $request->input('quantity') * ($request->input('price')+$request->input('off_loading'));
			$total_amount = $old_price+$new_price;
			$total_quantity = $request->input('quantity') + $res->quantity;
			$avg_price = $total_amount/$total_quantity;
			DB::table('sale_items')->where('id', $request->input('name'))->update(array('quantity' => $qty,'price'=>$avg_price));         
			$id = DB::table('sale_items_received')->insertGetId(array('quantity' => $request->input('quantity'),
				'price' => $request->input('price'),
				'supplier_id' => $request->input('supplier'),
				'off_loading' => $request->input('off_loading'),
				'date' => Session::get('todayDate'),
				'item_id' => $request->input('name')));
				
			DB::table('transection')->insert(array('amount' => $request->input('quantity')*$request->input('price'),
				'payment_type' => 'jama',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'Sale Items',
				'account_id' => $request->input('supplier'),
				'date' => Session::get('todayDate')));
				
			DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('price')),
				'payment_type' => 'naam',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'Sale Items',
				'account_id' => $res->category,
				'date' => Session::get('todayDate')));
			DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('off_loading')),
				'payment_type' => 'naam',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'سیل آئیٹم ان لوڈنگ خرچہ۔',
				'account_id' => 6,
				'date' => Session::get('todayDate')));
		}
		else{
			$qty = $res->quantity - $request->input('quantity');
			$old_price = $res->quantity * $res->price;
			//$off_loading = $request->input('off_loading');
			//$off_loading_per_piece = $request->input('off_loading') / $request->input('quantity');
			$new_price = $request->input('quantity') * ($request->input('price')+$request->input('off_loading'));
//			$new_price = $request->input('quantity') * $request->input('price');
			$total_amount = $old_price-$new_price;
			//$total_quantity = $request->input('quantity') + $res->quantity;
			if($qty == 0)
				$avg_price = 0;
			else
				$avg_price = $total_amount/$qty;
			DB::table('sale_items')->where('id', $request->input('name'))->update(array('quantity' => $qty,'price'=>$avg_price));         
			$id = DB::table('sale_items_received')->insertGetId(array('quantity' => $request->input('quantity'),
				'price' => ($request->input('price')+$request->input('off_loading')),
				'supplier_id' => $request->input('supplier'),
				'off_loading' => $request->input('off_loading'),
				'date' => Session::get('todayDate'),
				'type' => $request->input('type'),
				'item_id' => $request->input('name')));
				
			DB::table('transection')->insert(array('amount' => $request->input('quantity')*$request->input('price'),
				'payment_type' => 'naam',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'Sale Items return',
				'account_id' => $request->input('supplier'),
				'date' => Session::get('todayDate')));
				
			DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('price')),
				'payment_type' => 'jama',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'Sale Items return',
				'account_id' => $res->category,
				'date' => Session::get('todayDate')));
			DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('off_loading')),
				'payment_type' => 'jama',
				'sale_purchase_id' => $id,
				'type' => 'receive_item',
				'detail' => 'سیل آئیٹم ان لوڈنگ خرچہ۔',
				'account_id' => 6,
				'date' => Session::get('todayDate')));
		}
			
		$data['all_items'] = Sale_items::get();
		//$data['items'] = Sale_items::find($res->id);
		$data['items'] = Sale_items::where("Sale_items.id", $res->id)
			->join('sale_items_received as si', 'Sale_items.id', '=', 'si.item_id')
				->join('accounts as a', 'a.id', '=', 'si.supplier_id')
				->first(array('Sale_items.*','a.name as supplier'));
        return response($data);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = Sale_items::find($id);
        return response($item);
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

        Sale_items::where("id",$id)->update($input);
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
        return Sale_items::where('id',$id)->delete();
    }
	public function detail(Request $request)
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
		$total = 0;
		$query = DB::table('sale_items_received')->where('sale_items_received.deleted','no')
					->where('sale_items_received.item_id',$id)
					->whereBetween('sale_items_received.date', array($start_date, $end_date));
				$query->join('sale_items', 'sale_items.id', '=', 'sale_items_received.item_id');
			$data['data'] = $resultt = $query->get(array('sale_items_received.*','sale_items.name'));
			foreach($resultt as $row){
				$total += (($row->price * $row->quantity) + ($row->off_loading*$row->quantity));
			}
			$data['total'] = $total;
			return response($data);
	}
	public function sales_detail(Request $request)
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
		$total = 0;
		$query = DB::table('sales_items')->where('sales_items.deleted','no')->where('sales_items.item_id',$id)
					->whereBetween('sales.date', array($start_date, $end_date));
				$query->join('sale_items', 'sale_items.id', '=', 'sales_items.item_id');
				$query->join('sales', 'sales.id', '=', 'sales_items.sale_id','left');
			$data['data'] = $result = $query->get(array('sales_items.*','sale_items.name','sales.date'));
			foreach($result as $rrr){
				$total += (($rrr->price * $rrr->quantity) + (($rrr->price*$rrr->quantity)*($rrr->intrest/100)));
			}
			$data['total'] = $total;
			return response($data);
	}
	public function remove_receive_item($id)
    {
		$res = DB::table('sale_items_received')->where('id',$id)->first();
		$res1 = DB::table('sale_items')->where('id',$res->item_id)->first();
		$ttl_price = $res1->quantity*$res1->price;
		$ttl_delete_price = $res->quantity*$res->price;
		$qty = $res1->quantity-$res->quantity;
		$price = $ttl_price-$ttl_delete_price;
		if($qty == 0){
			$avg_price = 0;
		}
		else{
			$avg_price = $price/$qty;
		}
		DB::table('sale_items')->where('id', $res->item_id)->update(array('quantity' => $qty,'price'=>$avg_price));         
		DB::table('sale_items_received')->where('id', $id)->update(array('deleted' => 'yes'));
		DB::table('transection')->where('sale_purchase_id', $id)->where('type','receive_item')->update(array('deleted' => 'yes'));
		
		//Mallrokkar::where("id",$id)->update(['deleted' => 'yes']);
		return $id;
        //return Account::where('id',$id)->delete();
    }
	
	
	/////////first sale items first ///////////////
	public function saleitemsFirst(Request $request)
    {
    	$input = $request->all();
        //$create = Sale_items::create($input);
		$create_id = DB::table('Sale_items')->insertGetId(array(
			'name' => $request->input('name'),
			'quantity' => $request->input('quantity'),
			'price' => ($request->input('price')+$request->input('off_loading')),
			'category' => $request->input('category')
			));
		$data['all_items'] = Sale_items::get();
		
		$id = DB::table('sale_items_received')->insertGetId(array('quantity' => $request->input('quantity'),
			'price' => $request->input('price'),
			'supplier_id' => $request->input('supplier'),
			'off_loading' => $request->input('off_loading'),
			'date' => Session::get('todayDate'),
			'item_id' => $create_id));
			
		/*DB::table('transection')->insert(array('amount' => $request->input('quantity')*$request->input('price'),
			'payment_type' => 'jama',
			'sale_purchase_id' => $id,
			'type' => 'receive_item',
			'detail' => 'Sale Items',
			'account_id' => $request->input('supplier'),
			'date' => Session::get('todayDate')));*/
		
		DB::table('transection')->insert(array('amount' => (($request->input('quantity')*$request->input('price'))+($request->input('quantity')*$request->input('off_loading'))),
			'payment_type' => 'naam',
			'sale_purchase_id' => $id,
			'type' => 'receive_item',
			'detail' => 'Sale Items',
			'first' => 'yes',
			'account_id' => $request->input('category'),
			'date' => Session::get('todayDate')));
			if($request->input('off_loading') != 0){
				DB::table('transection')->insert(array('amount' => ($request->input('quantity')*$request->input('off_loading')),
					'payment_type' => 'naam',
					'sale_purchase_id' => $id,
					'type' => 'receive_item',
					'detail' => 'سیل آئیٹم ان لوڈنگ خرچہ۔',
					'account_id' => 6,
					'first' => 'yes',
					'date' => Session::get('todayDate')));
			}
		$data['create'] = Sale_items::where("Sale_items.id", $create_id)
			->join('sale_items_received as si', 'Sale_items.id', '=', 'si.item_id')
				->join('accounts as a', 'a.id', '=', 'si.supplier_id')
				->first(array('Sale_items.*','a.name as supplier'));
        return response($data);
    }
	///////// end sale items first //////////
	
	function date_change($date)
	{
		$new_date = explode('/',$date);
		return $new_date[2].'-'.$new_date[1].'-'.$new_date[0];
	}
}
