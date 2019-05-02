<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Product;
use App\Review;
use App\WishList;
use App\Category;

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
      
      $footerCategories = Category::getFooterCategories();

      return view('pages.product', ['product' => $product, 'reviews' => $reviews, 'reviewsStats' => $reviewsStats, 'wishlist' => $wishlist, 'categories' => $footerCategories]);
    }

    /**
     * Creates a new product.
     *
     * @return Card The product created.
     */
    public function create(Request $request)
    {
      $product = new Product();

      $this->authorize('create', $product);

    //   $product->name = $request->input('name');
    //   $product->user_id = Auth::user()->id;
      $product->save();

      return $product;
    }
}
