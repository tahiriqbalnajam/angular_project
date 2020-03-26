<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserPhone extends Model
{
	protected $table = 'user_phone';
    public $fillable = ['phone_no','token'];

}
