<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderBookList;

class OrderMatchingService
{
    //
  public function PlaceBuyBid($customer_id, $quantity, $price , $datetime=null ) {

  }
  public function PlaceSellBid($customer_id , $quantity, $price, $datetime=null ) {

  }
  public static function isValidOrder($customer_id, $quantity, $price , $datetime=null ) {
    return true;
  }
}
