<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Account_type;
use Validator;
use Auth;


class UserController extends Controller
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
		
		$data['account_type'] = Account_type::all();
        if($request->get('search') && $request->get('account_type')){
            $data['account'] = Account::where("name", "LIKE", "%{$request->get('search')}%")
				->where("account_type", "{$request->get('account_type')}")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate(5,['accounts.id AS account_id', 'accounts.*','account_types.id','account_types.title']);     
        }
		else if($request->get('search') && $request->get('account_type') == ''){
            $data['account'] = Account::where("name", "LIKE", "%{$request->get('search')}%")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate(5,['accounts.id AS account_id', 'accounts.*','account_types.id','account_types.title']);     
        }
		else if($request->get('account_type') && $request->get('search') == ''){
			$acnt_type = ($request->get('account_type'))?$request->get('account_type'):'';
            $data['account'] = Account::where("account_type","{$request->get('account_type')}")
				->join('account_types', 'accounts.account_type', '=', 'account_types.id')
		  		->paginate(5,['accounts.id AS account_id', 'accounts.*','account_types.id','account_types.title']);     
        }
		else{
		  $data['user'] = User::paginate(5);
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
		if(isset($data['password']))
			$data['password'] = bcrypt($data['password']);

        User::where("id",$id)->update($input);		
        return response($data);
    }
	public function destroy($id)
    {
        return User::where('id',$id)->delete();
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
