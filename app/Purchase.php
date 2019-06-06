<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Cart;

class Purchase extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'purchase';

    public static function getProductsFromPurchase($id)
    {
        return DB::table('purchased_product')
        ->select('id_product', 'name', 'price', 'discount', 'quantity')
        ->where('id_purchase', $id)
        ->get();
    }

    public static function purchaseProduct($product, $id_purchase)
    {
        DB::table('purchased_product')->insert(
            ['id_product' => $product->id_product,
            'id_purchase' => $id_purchase,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'discount' => $product->discount,
            'quantity' => $product->quantity]
        );
    }

    public static function purchase($id_client, $id_billing)
    {
        $products = Cart::getProductsFromCart($id_client);
        Cart::removeAllProductsFromCart($id_client);

        $id_purchase = DB::table('purchase')->insertGetId(
            ['id_billing_information' => $id_billing, 'id_client' => $id_client]
		);

		foreach ($products as $key => $value) {
			foreach ($products as $key2 => $value2) {
				if($key < $key2 && $value->id_product == $value2->id_product) {
					$value->quantity += $value2->quantity;
					$products->forget($key2);
				}
			}
		}

        foreach ($products as $product) {
            Purchase::purchaseProduct($product, $id_purchase);
        }
    }

    public static function getProductsWaitingForConfirmation()
    {
        $aux = PurchaseLog::select('id_purchase')
        ->where('purchase_state', '>', 'Waiting for payment approval')
        ->get();

        return PurchaseLog::selectRaw('id_purchase, max(purchase_state) as purchase_state, max(date_time) as date_time')
        ->whereNotIn('id_purchase', $aux)
        ->groupBy('id_purchase')
        ->paginate(10);
    }

    public static function isWaitingShipment($client_id, $product_id) {
        return DB::table('purchase')
            ->join('purchase_log','purchase_log.id_purchase','purchase.id')
            ->join('purchased_product','purchased_product.id_purchase','purchase.id')
            ->where('id_client', $client_id)
            ->where('purchase_state',"Shipped")
            ->where('id_product',$product_id)
            ->first();
    }
}
