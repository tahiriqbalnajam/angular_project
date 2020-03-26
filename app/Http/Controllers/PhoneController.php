<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use View;
use Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Html\HtmlFacade;
use App\UserPhone;
use DB;
use Cookie;
use Session;
use Response;

class PhoneController extends Controller
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

	public function get_phone_form()
	{


		return  view('phone');

	}

	public function save_phone(Request $request)
	{
	
		

		$rules = array(
			
			'phone'      => 'required',
			
		
			
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return  Redirect::to('PhoneForm')
				->withErrors($validator)
				->withInput(Input::except('password'));
		} else {

				
				$phone = UserPhone::firstOrNew(array('phone_no'=>$request->phone));
				$phone->phone_no =$request->phone;
				
				$chk_phone = DB::table('user_phone')
 	 			->where('phone_no',$request->phone)
 	 			->first();
				if(isset($chk_phone))
				{
					return  Redirect::to('login/');
					
				}else{
				
				
					$phone->token = $token = str_random('5');
					$phone->save();
						
					/////////// send sms///////
					$username ="923217050405" ;
					$password ="8921" ;
					$sender = "IDLBridge" ;
					$mobile = $request->phone;
					
					$message = "Dear Customer\nWelcome to IDLMunshi\nYour key is : ".$token."\nThank You\nIDLBridge Team\nFor more detail please dail 03457050405";								
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
					////////// end send sms///////
						
						
					return  Redirect::to('KeyForm');
				}
	  
	  }
	}



	public function key_form()
	{

      return  view('enter_key');

	}


  public function key_enter(Request $request)
  {
 	$token = $request->token;
 	$coockiename ='token';
 	$cookieval =$token;
 
 
 	setcookie($coockiename, $request->token, time()+60*60*24*365);
   
 	$coockie =  Cookie::get($coockiename);
 	Session::set('token', $request->token);
 	$session=Session::get('token');
 	$phone = DB::table('user_phone')
 	 			->where('token',$session)
 	 			->first();
 	if(isset($phone))
 	{
 		return  Redirect::to('login/');
 		
 	}else{

 		Session::flash('error', 'Invalid Key');
       return Redirect::to('KeyForm');
 		
 	  } 	

  }
	
	
 public function logout()
 {
 	Session::flush();
   /* return Redirect::to('PhoneForm')->withCookie($cookie);*/
   return  Redirect::to('PhoneForm');

 }
	

    
    
}
