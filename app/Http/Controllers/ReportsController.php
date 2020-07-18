<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Account;
use App\Account_type;
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
        $data['accounts'] = Account::with('account_type')->get();
		return response($data);
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
