<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Mallrokkar extends Model
{
	protected $table = 'mallroakker';

    public $fillable = ['saler_id','purchaser_id','saller_amount','purchaser_amount','account_type','status','percha_no'];

	public function get_account_type()
	{
		return Account_type::all();
	}
}
