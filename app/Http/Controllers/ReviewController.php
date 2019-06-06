<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Review;


class ReviewController extends Controller
{
    public function create(Request $request)
    {
      $review = new Review();

      // $this->authorize('create', $user);

      $review->id_product = intval($request->input('id_product'));
      $review->id_client = Auth::user()->id;
      $review->comment = $request->input('comment');
      $review->rating = $request->input('rating');

      $review->save();

      $review->username = Auth::user()->username;
      $review->date_time = Review::getReviewDate($review->id_client, $review->id_product);
      $review->reviewsStats = Review::getProductReviewsStats($review->id_product);
      return $review;
    }

    public function update(Request $request, $id){
        $review = Review::find($id);

        $review->comment = $request->input('comment');
        $review->rating = $request->input('rating');
        
        $review->save();

        return $review;
    }
}
