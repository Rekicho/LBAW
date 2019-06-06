<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

    public static function getProductReviews($id){
        return DB::table('reviews')->join('users', 'reviews.id_client', '=', 'users.id')->select('reviews.id', 'users.username', 'reviews.comment', 'reviews.rating', 'reviews.date_time')->where('id_product', $id)->get();
    }

    public static function getProductReviewsStats($id){
        return DB::table('reviews')->select(DB::raw('COUNT(id) as numRatings, AVG(rating) AS rating'))->where('id_product', $id)->first();
    }

    public static function reviews(){
        return Review::join('products', 'products.id', 'reviews.id_product')
        ->join('users', 'users.id', 'reviews.id_client')
        ->paginate(10);
    }

    public static function getReview($client,$product) {
        return DB::table('reviews')
                ->where("id_product",$product)
                ->where("id_client",$client)
                ->first();
    }

    public static function getReviewDate($client,$product){
        return DB::table('reviews')
                ->select('date_time')
                ->where("id_product",$product)
                ->where("id_client",$client)
                ->first();
    }
}
