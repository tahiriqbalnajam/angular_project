<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Running_sale_items extends Model
{

    public $fillable = ['seller_id','purchaser_id','purchase_price','sale_price','quantity','date','deleted','item_id','sale_id','saler_percentage','purchaser_percentage','price'];

}
