<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Product;
use App\Review;
use App\WishList;

class ProductController extends Controller
{
    /**
     * Shows the card for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
      $product = Product::getProductInfo($id);
      
      $reviews = Review::getProductReviews($id);

      $reviewsStats = Review::getProductReviewsStats($id);

      $wishlist = WishList::exists(Auth::user()->id, $id);
      
      return view('pages.product', ['product' => $product, 'reviews' => $reviews, 'reviewsStats' => $reviewsStats, 'wishlist' => $wishlist]);
    }

    /**
     * Creates a new product.
     *
     * @return Card The product created.
     */
    public function create(Request $request)
    {
      $product = new Product();

      //$this->authorize('create', $product);

      // $request->file('image')->store('public/img');

      $product->name = $request->input('name');
      $product->description = "ya"; //($request->input('description');
      $product->is_enabled = true;
      $product->id_category = $request->input('category');
      $product->price = $request->input('price');
      $product->discount = 0;
      $product->stock = $request->input('stock');

      $product->save();

      return $product;
    }
}
