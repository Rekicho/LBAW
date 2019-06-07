<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

use App\Product;
use App\Review;
use App\WishList;
use App\User;
use App\Cart;
use App\Category;
use App\Purchase;

use App\Notifications\ProductOnSale;

class ProductController extends BaseController
{
    /**
     * Shows the card for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
      try {
          $product = Product::getProductInfo($id);
          if ($product == null) {
              return view('errors.page_not_found', ['error' => 'That product doesn\'t exist yet!']);
          }
      } catch (QueryException $e) {
          Log::error("User tried to access nonexistant product", ['id' => $id]);
          return view('errors.page_not_found', ['error' => 'Product not found!']);
      }
      
      $product->price = round($product->price * ((100 - Product::getDiscount($id)) / 100),2);

      $reviews = Review::getProductReviews($id);

      $reviewsStats = Review::getProductReviewsStats($id);

      if(Auth::user()){
          $wishlist = WishList::exists(Auth::user()->id, $id);
          $cart = Cart::exists(Auth::user()->id, $id);
          $canReview = $this->canReview(Auth::user()->id,$id);
      } else {
         $wishlist = [];
         $cart = [];
         $canReview = false;
      }
      
      // Check if the user can review
  
      return view('pages.product', ['product' => $product, 'reviews' => $reviews, 'reviewsStats' => $reviewsStats, 'wishlist' => $wishlist, 'cart' => $cart, 'canReview' => $canReview]);
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

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products',
            'description' => 'required|string|min:100',
            'category' => 'required',
            'image' => 'required|image|mimes:png|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()->all()]);
        }

        $image_name = 'product' . strval(Product::orderBy('id', 'desc')->first()->id + 1) . '.png';
        $path = $request->file('image')->storeAs(
            '/public/img', $image_name
        );

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->is_enabled = true;
        $product->id_category = $request->input('category');
        $product->price = $request->input('price');
        $product->discount = 0;
        $product->stock = $request->input('stock');

        $product->save();

        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $type = $request->input('type');

        if ($type === "stock") {
            $product->stock = $request->input('stock');
        } elseif ($type === "price") {
            $product->price = $request->input('price');
        } elseif ($type === "discount") {
            $prevDiscount = $product->discount;
            $product->discount = $request->input('discount');

            $users = Wishlist::usersWishlisted($product->id);

            Notification::send($users, new ProductOnSale($product));

            return $users;
        } else {
            $is_enabled = $request->input('is_enabled');
            $product->is_enabled = $is_enabled === 'true' ? true : false;
        }


        $product->save();

        return $product;
    }

    public function canReview($client, $product) {
      //Este client ja tem de ter comprado o producto e ja tem de estar a espera
      $isWaitingShipment = Purchase::isWaitingShipment($client,$product);
      $hasReviewd = Review::getReview($client,$product);
      
      if($isWaitingShipment && !$hasReviewd){
        return true;
      }else{
        return false;
      }
    }
}
