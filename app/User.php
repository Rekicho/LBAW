<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'facebook_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function staff_members(){
       return DB::table('users')->select('id', 'username', 'is_enabled', 'is_staff_member')->where('is_staff_member', true)->paginate(10);
    }

    public static function clients(){
        return User::where('is_staff_member', false)->paginate(10);
    }

    public static function purchaseHistory($userId){

        $purchases = DB::table('purchase')
        ->selectRaw('id, date_time::date, date_time AS time')
		->where('id_client', $userId)
		->orderByRaw('time DESC')
        ->get();
  
        foreach ($purchases as $purchase){
           $purchase->logs = DB::table('purchase_log')
           ->selectRaw('id, purchase_state, date_time::date')
           ->where('id_purchase', $purchase->id)
           ->get();

           $products = Purchase::getProductsFromPurchase($purchase->id);

           $sum = 0;
           foreach($products as $product)
            $sum+= $product->price*$product->quantity;

            $purchase->products = $products;
            $purchase->price = $sum;
        }
  
        return $purchases;
    }

    public function addNew($input)
    {
        $check = static::where('facebook_id',$input['facebook_id'])->first();


        if(is_null($check)){
            return static::create($input);
        }


        return $check;
    }
}
