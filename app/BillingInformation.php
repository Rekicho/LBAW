<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BillingInformation extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  protected $table = 'billing_information';

  public static function billingInformation($userId){
    return DB::table('billing_information')
    ->select('id', 'full_name', 'address', 'city', 'state', 'zip_code')
    ->where('id_client', $userId)->first();
  }
}
