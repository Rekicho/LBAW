<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public static function reports(){
        return Report::join('users', 'users.id', 'reports.id_client')
        ->join('reviews', 'reviews.id', 'reports.id_review')
        ->join('products', 'reviews.id_product', 'products.id')
        ->join('report_log', 'report_log.report_id', 'reports.id')
        ->paginate(10);
    }
}
