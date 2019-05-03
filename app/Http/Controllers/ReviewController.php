<?php

namespace App\Http\Controllers;

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
