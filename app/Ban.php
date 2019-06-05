<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    public $timestamps  = false;

    public static function getLastBan($user){
        return Ban::where('id_client', $user->id)->orderBy('start_t', 'desc')->first();
    }
}
