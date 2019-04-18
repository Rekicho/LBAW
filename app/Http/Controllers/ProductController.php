<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Product;

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
      $product = DB::table('products')->join('categories', 'products.id_category', '=', 'categories.id')->join('reviews', 'products.id' , '=', 'reviews.id_product')
      ->select(DB::raw('products.name AS prodname, price, description, discount, stock, categories.name AS catname, COUNT(reviews.id) AS numRatings, AVG(reviews.rating) AS rating'))->where('products.id', $id)->groupBy('products.name', 'products.price', 'products.description', 'products.discount', 'products.stock', 'categories.name')->first();

      $reviews = DB::table('reviews')->join('users', 'reviews.id_client', '=', 'users.id')->select('reviews.id', 'users.username', 'reviews.comment', 'reviews.rating', 'reviews.date_time')->where('id_product', $id)->get();

      return view('pages.product', ['product' => $product, 'reviews' => $reviews]);
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

    public function delete(Request $request, $id)
    {
      $card = Card::find($id);

      $this->authorize('delete', $card);
      $card->delete();

      return $card;
    }
}
