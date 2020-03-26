<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Sale_items extends Model
{

    public $fillable = ['name','quantity','price','deleted','category'];

}
