<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Dir extends Model
{

    public $fillable = ['name','phone','address','work','deleted'];

}
