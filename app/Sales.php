<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Sales extends Model
{

    public $fillable = ['account_id','quantity','price','date','detail'];

}
