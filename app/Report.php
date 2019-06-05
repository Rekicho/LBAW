<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public static function reports(){
        return Report::paginate(10);
    }
}
