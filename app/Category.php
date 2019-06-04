<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
	public static function getAllCategories(){
		return DB::table('categories')->select('id', 'name')->get();
	  }
}