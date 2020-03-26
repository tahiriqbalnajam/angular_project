<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Account;
use App\Account_type;
use DB;
use Session;

class AccountTypeController extends Controller
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
		//$data['account_type'] = Account_type::all();
        if($request->get('search')){
            $data['account_type'] = Account_type::where("title", "LIKE", "%{$request->get('search')}%")								
		  		->where("deleted","no")->paginate($data['perpage'],['id', 'title','deleted']);     
        }		
		else{
		  $data['account_type'] = Account_type::where("deleted","no")->paginate($data['perpage'],['id', 'title','deleted']);
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
	 
    public function store(Request $request)
    {
		
    	$input = $request->all();
        $create = Account_type::create($input);
		$create1 = Account_type::select('id', 'title','deleted')
		  		->where("id",$create->id)->first();
        return response($create1);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $account = Account_type::find($id);
        return response($account);
    }
	public function destroy($id)
    {
		DB::table('account_types')->where('id', $id)->update(['deleted' => 'yes']);
		
		//Mallrokkar::where("id",$id)->update(['deleted' => 'yes']);
		return $id;
        //return Account::where('id',$id)->delete();
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

        Account_type::where("id",$id)->update($input);
        //$account = Account::find($id);
		$account = Account_type::select('id', 'title','deleted')
		  		->where("id",$id)->first();
        return response($account);
    }
	
}
