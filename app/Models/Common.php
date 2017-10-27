<?php

namespace App\Models;

use App\Models\OrderTransaction;
use DB;
class Common
{
    //
    public function __construct() {

    }
    public static function udate($format, $utimestamp = null) {
      if (is_null($utimestamp))
        $utimestamp = microtime(true);

      $timestamp = floor($utimestamp);
      $milliseconds = round(($utimestamp - $timestamp) * 1000000);

      return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
    public static function UTCToTimestamp($utc_datetime_str)
    {
        preg_match_all('/(.+?)T(.+?)\.(.*?)Z/i', $utc_datetime_str, $matches_arr);
        $datetime_str = $matches_arr[1][0]." ".$matches_arr[2][0];
    
        return strtotime($datetime_str);
    }
      
    public static function getTradePrice( $want_asset, $offer_asset ) {
      $orderTransModel = new OrderTransaction();
      $lastTradePrice = $orderTransModel->getLastTradePrice($want_asset, $offer_asset);
      if ( $lastTradePrice == 0 ) return self::changellyAPI($want_asset, $offer_asset);
      return $lastTradePrice;
    } 
    
    public static function changellyAPI($want_asset, $offer_asset) {
      $apiKey = '59201ddd08c849228dcd6bacf4cf4279';
      $apiSecret ='57a1d9376a85b231b3ab8df0afb7c7119976a9c34661cedbdc83c8fb21304bbe';
      $apiUrl = 'https://api.changelly.com';
      $message = json_encode(array('jsonrpc'=>'2.0', 'id'=>1, 'method'=>'getExchangeAmount', 'params'=>array('from'=>strtolower($want_asset),'to'=>strtolower($offer_asset),'amount'=>1)));
      $sign = hash_hmac('sha512', $message, $apiSecret);
      $requestHeaders = [
          'api-key:' . $apiKey,
          'sign:' . $sign,
          'Content-type: application/json'
      ];
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

      $response = json_decode(curl_exec($ch));
      curl_close($ch);
      return $response->result;
    }
    public static function std_to_array( $stdObj ) {
      $ret_arr = array();
      foreach( $stdObj as $key=>$val ) {
        $ret_arr[$key] = $val;
      }
      return $ret_arr;
    }
}
