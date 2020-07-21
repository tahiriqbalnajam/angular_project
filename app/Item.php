<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Item extends Model
{

    public $fillable = ['title','price','description'];
	public function running_sale_items() {
    	return $this->hasMany('App\Running_sale_items','item_id');
	}

}
