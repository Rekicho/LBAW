<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseLog extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  protected $table = 'purchase_log';
}
