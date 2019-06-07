<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps  = false;

    public static function reports(){

        return Report::selectRaw('reports.id, reports.reason, products.name, products.id AS id_product, products.name, users.username')
        ->join('users', 'users.id', 'reports.id_client')
        ->join('reviews', 'reviews.id', 'reports.id_review')
        ->join('products', 'reviews.id_product', 'products.id')
        ->whereNotIn('reports.id', function ($q) {
            $q->select('report_log.id_report')->from('report_log');
        })
        ->paginate(10);
    }
}
