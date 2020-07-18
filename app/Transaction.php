<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transection';
    public function sale() {
        return $this->belongsTo('App\Sales', 'sale_purchase_id');
    }
}
