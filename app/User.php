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
        'username', 'email', 'password',
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
       return DB::table('users')->select('id', 'username', 'is_enabled')->where('is_staff_member', true)->paginate(10);
    }

    public static function purchaseHistory($userId){
        $purchases = DB::table('purchase')
        ->join('purchase_log', 'purchase.id', '=', 'purchase_log.id_purchase')
        ->select('purchase_log.id_purchase', 'purchase.date_time AS purchase_date', 'purchase_log.date_time AS log_date', 'purchase_state')
        ->where('purchase.id_client', $userId)
        ->groupBy('purchase_log.id_purchase', 'purchase.date_time', 'purchase_log.date_time', 'purchase_state')
        ->orderBy('purchase.date_time', 'desc')
        ->get();
  
        // foreach ($purchases as $purchase){
        //   $purchase->products = Purchase::getProductsFromPurchase($purchase->id_purchase);
        // }
  
        return $purchases;
      }
}
