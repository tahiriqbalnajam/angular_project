<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }
	
	public function showLogin()
	{
		die('asdf');
		// show the form
		return \View::make('users.login');
	}
	
	
	public function doLogin()
	{
		//die('asdfsdafsdf');
		// validate the info, create rules for the inputs
		$rules = array(
			'email'    => 'required|email', // make sure the email is an actual email
			'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
		);
		
		// run the validation rules on the inputs from the form
		$validator = \Validator::make(Input::all(), $rules);
		
		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
			return Redirect::to('login')
				->withErrors($validator) // send back all errors to the login form
				->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
		} else {
		
			// create our user data for the authentication
			$userdata = array(
				'email'     => Input::get('email'),
				'password'  => Input::get('password')
			);
		
			// attempt to do the login
			if (Auth::attempt($userdata)) {
				
				if (Auth::user()->status =='enable') {
		
  				$config = Config::findOrFail(1);
				
				session(['company_name' => $config->company_name]);
				 
			    
                     
				session(['user_role' => Auth::user()->user_role]);
				
			  	if(session()->get('user_role') == '1'){
			   		return Redirect::to('home/');
		      	}
			  	else
				{
					$labname = Labs::where('id',Auth::user()->lab_id)->pluck('lab_name')[0];
					session(['lab_id' => Auth::user()->lab_id]);
					session(['username' => Auth::user()->username]);
					session(['lab_name' => $labname]);
					return Redirect::to('home-lab/');
				}
				}else
				{
					
					return Redirect::to('admin/login')->withErrors(['User is disable']);
					
				}
				
		    
			} else {        
		
				// validation not successful, send back to form 
				
				return Redirect::to('admin/login')->withErrors(['Email or Password not correct']);
		
			}
		
		}
		
	}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
}
