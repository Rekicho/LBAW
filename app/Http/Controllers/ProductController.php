<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\QueryException;

use App\Product;
use App\Review;
use App\WishList;
use App\User;
use App\Cart;
use App\Category;
use App\Cart;

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
            return view('errors.page_not_found', ['error' => 'Product not found!']);
        }
      
        $reviews = Review::getProductReviews($id);

        $reviewsStats = Review::getProductReviewsStats($id);

        if (Auth::user()) {
            $wishlist = WishList::exists(Auth::user()->id, $id);
            $cart = Cart::exists(Auth::user()->id, $id);
        } else {
            $wishlist = array();
            $cart = array();
        }
      
        return view('pages.product', ['product' => $product, 'reviews' => $reviews, 'reviewsStats' => $reviewsStats, 'wishlist' => $wishlist, 'cart' => $cart]);
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

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products',
            'description' => 'required|string|min:100',
            'category' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()->all()]);
        }

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
}
