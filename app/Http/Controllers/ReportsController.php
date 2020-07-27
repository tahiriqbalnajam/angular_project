<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\Account;
use App\Account_type;
use App\Sale_items;
use App\Reports;
use App\Running_sale_items;
use Validator;
use Auth;


class ReportsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data['items'] = Item::get();
		$data['sale_items'] = Sale_items::get();
		
		$date = ($request->get('date'))?$request->get('date'):'';
		$items_id = ($request->get('item_id'))?$request->get('item_id'):'';
		if($date){
			$new_date = explode(' - ',$date);
			$start_date = $this->date_change($new_date[0]);
			$end_date = $this->date_change($new_date[1]);
		}
		else{
			$start_date = date('Y-m-d');
			$end_date = date('Y-m-d');
		}
		
		/*if($items_id) {
			$data['item_detail'] = Item::with(array('running_sale_items' => function($query) use ($start_date,$end_date){
																		$query->whereBetween('date', [$start_date, $end_date])
																			->where('deleted','no');
																		}))->where('id',$items_id)->paginate(4000);
		}*/
		
		
		$reports = new Reports();
		
		if($items_id)
			$data['items_detail'] = $reports->get_direct_item_detail($start_date,$end_date,$items_id);
			
		return response($data);
    }
	function date_change($date)
	{
		$new_date = explode('/',$date);
		return $new_date[2].'-'.$new_date[1].'-'.$new_date[0];
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	protected function validator(array $data)
    { 
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
	
	public function store(Request $request)
    { 
		$data = $request->all();
		//$labs = Labs::all();
		
		
		if(sizeof($data)){
			
			if (Validator::make($data, array('name' => 'required|max:255','email' => 'required|email|max:255|unique:users','password' => 'required|confirmed|min:6')))                   
			{
				User::create(array('name' => $data['name'],
					'email' => $data['email'],
					'password' => bcrypt($data['password']),
        		));
			/*	if($this->create($data))
					response($data);*/
			}
		}
		return $data;
    } 
    /*public function store(Request $request)
    {
		
    	$input = $request->all();
        $create = Account::create($input);
		$create1 = Account::select('accounts.id AS id', 'accounts.*','account_types.id  as typeid','account_types.title')
		->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->where("accounts.id",$create->id)->first();
        return response($create1);
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $account = User::find($id);
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
    	$data = $request->all();
		$input = array('name' => $data['name']
        		);
		if(isset($data['password'])){
			$data['password'] = bcrypt($data['password']);
			$input['password'] = $data['password'];
		}

        User::where("id",$id)->update($input);		
        return response($data);
    }
	public function destroy($id)
    {
        return User::where('id',$id)->delete();
    }
	
	public function get_items_detail()
	{
		$data['accounts'] = Account::with('account_type')->get();
		return response($data);
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
    
	
}
