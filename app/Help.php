<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Help extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

    public static function getHelpText($id){
        return DB::table('help')->select('description','help_text')->where('id',$id)->first();
    }
}
