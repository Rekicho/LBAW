<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Ban extends Model
{
    public $timestamps = false;
    protected $table = 'bans';

    public static function getLastBan($user){
        return Ban::where('id_client', $user->id)->orderBy('start_t', 'desc')->first();
    }
}
