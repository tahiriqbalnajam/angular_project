<?php

namespace App\Http\Controllers\Auth;

use App\User;

use Validator;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ThrottlesLogins;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use View;
use App\Labs;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Html\HtmlFacade;
use DB;
use Auth;
use Session;




class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    //use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest', ['except' => 'logout']);
    }
	public function index()
	{
		//die('asdf');
		return View::make('welcome');
		
	}
	protected function showLoginForm(){
		//die('sadf');
		return View::make('auth.login');
	}
	public function login()
	{
		$zaki = explode(' ',exec('getmac'));
		//t-d89be3b6a91c4230854e2746f05114c5
		//n-59f969b41d62428f5073edebe49d9450

		if(md5($zaki[0]) == '59f969b41d62428f5073edebe49d9450' ){
			// validate the info, create rules for the inputs
		$rules = array(
			'email'    => 'required|email', // make sure the email is an actual email
			'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
		);
		
		// run the validation rules on the inputs from the form
		$validator = \Validator::make(Input::all(), $rules);
		//print_r($validator);
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
				$res = DB::table('closing_date')->latest('id')->first();
					
				Session::set('todayDate', $res->date);	
				$amount = DB::table('amount_in_hand')->where('date',Session::get('todayDate'))->first();
				Session::set('amountInHand', $amount->amount);
				return Redirect::to('dashboard/');				
				/*
				
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
					
					return Redirect::to('login')->withErrors(['User is disable']);
					
				}
				
		    
			*/} else {        
		
				// validation not successful, send back to form 
				
				return Redirect::to('login')->withErrors(['Email or Password not correct']);
		
			}
		
		}
		}
		else{
			return Redirect::to('login')->withErrors(['Sorry! You have not license for this machine']);
		}
		
		
	}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
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
	
	public function showRegistrationForm()
    {
		$data = Input::all();
		//$labs = Labs::all();
		
		
		if(sizeof($data))
			if ($this->validator($data))
			{
				if($this->create($data))
					return view('auth.login');
			}
		return View::make('auth.register');
    }
	
	public function logout(Request $request)
	{
		$request->session()->flush();
		$request->session()->regenerate();
		return redirect('login');
	}
}
