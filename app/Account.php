<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Account extends Model
{

    public $fillable = ['name','address','phone','cnic','account_type','status','account_number','account_number_old','check_from','check_to','type','sms','opening_balance'];            

	public function get_account_type()
	{
		return Account_type::all();
	}
}
