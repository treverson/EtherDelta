<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderMatchingService;
use App\Models\OrderTransaction;
use DB;

class OrderBookList extends Model
{
    //
    protected $table = 'orderbook';
    public $order;

    public function getPlaceBuyBid( $order_id ) {
      $order = $this->where('order_id', '=', $order_id)->first()->toArray();

      if (OrderMatchingService::isValidOrder($order['customer_id'], $order['quantity'], $order['price'], $order['expiration_date'])) {
        $this->where('order_id', '=', $order_id)->update(['order_status'=>'open', 'updated_at'=>date('Y-m-d H:i:s')]);
        return true;
      }
      return false;
    }
    public function getPlaceSellAsk( $order_id ) {
      $order = $this->where('order_id', '=', $order_id)->first()->toArray();

      if (OrderMatchingService::isValidOrder($order['customer_id'], $order['quantity'], $order['price'], $order['expiration_date'])) {
        $this->where('order_id', '=', $order_id)->update(['order_status'=>'open', 'updated_at'=>date('Y-m-d H:i:s')]);
        return true;
      }
      return false;
    }
    public function getOrderListForBuy($price, $customer_id, $offer_asset, $want_asset) {
      $data = $this->where('customer_id', '<>', $customer_id)->where('order_side','=', 'sell')->where('price', '<=', $price)->where('order_status', '=', 'open')->orderBy('price', 'asc')->orderBy('updated_at', 'asc');
      return $data->get()->toArray();
    }
    public function getOrderListForSell($price, $customer_id, $offer_asset, $want_asset) {
      $data = $this->where('customer_id', '<>', $customer_id)->where('order_side','=', 'buy')->where('price', '>=', $price)->where('order_status', '=', 'open')->orderBy('price', 'desc')->orderBy('updated_at', 'asc');
      return $data->get()->toArray();
    }
    public function setOrder( $order_id ) {
      $this->order = $this->where('order_id', '=', $order_id)->first()->toArray();
    }
    public function matchOrders( $matchData ) {
      $quantity = $this->order['quantity'];
      $ret_arr = array();
      $order_trade_arr = array();
      $orderTransModel = new OrderTransaction();
      $trade_price = $orderTransModel->getTradePrice($this->order['offer_asset'], $this->order['want_asset']);
      foreach( $matchData as $match_order ) {
        $fee = 0;
        $match_order_filled_amount = $orderTransModel->getFilledAmountB($match_order['order_id']);
        $quantity_amount = $match_order['quantity']-$match_order_filled_amount;
        //if ( $quantity < 0 ) break;
        if ( $quantity <= 0 ) {
          $this->where('order_id', '=', $this->order['order_id'])->update(['order_status'=>'closed']);
          break;
        }
        if ($quantity_amount <= 0) continue;
        if ( $quantity_amount < $quantity ) {
          $ret_arr['a_amount'] = $quantity_amount;
          $ret_arr['b_amount'] = $quantity_amount;
          $this->where('order_id', '=', $match_order['order_id'])->update(['order_status'=>'closed']);
        }
        else {
          $ret_arr['a_amount'] = $quantity;
          $ret_arr['b_amount'] = $quantity;
        }
        $ret_arr['a_commission'] = $fee;
        $ret_arr['a_order_id'] = $this->order['order_id'];
        $ret_arr['b_order_id'] = $match_order['order_id'];
        $ret_arr['offer_asset'] = $this->order['offer_asset'];
        $ret_arr['want_asset'] = $this->order['want_asset'];
        if ($this->order['order_type']=='limit')
          $ret_arr['trade_price'] = $this->order['price'];
        elseif ($this->order['order_type']=='stop') {
          # code...
          if ( isset($this->order['stop_price']) && !isset($this->order['price']) )
            $ret_arr['trade_price'] = $trade_price;
          else if (isset($this->order['stop_price']) && isset($this->order['price']))
            $ret_arr['trade_price'] = $this->order['stop_price'];
        }
        else {
          $ret_arr['trade_price'] = $match_order['price'];
        }
        $orderTransModel->addOrderTransaction($ret_arr);
        $quantity -= $quantity_amount;
        $order_trade_arr[] = $ret_arr;
      }
      dd($this->order,$matchData, $order_trade_arr, $quantity);
    }

    public function getOpenOrderList($product) {
      $tmp = explode('-', $product);
      $offer_asset = $tmp[0];
      $want_asset = $tmp[1];
      $customer_id = 1;
      $sql = "select
              a.quantity size, a.price, 0 fee, a.updated_at time, a.order_status status, a.order_side,
              a.order_type, a.expiration_date, a.order_id, a.want_asset, a.offer_asset, a.customer_id, a.time_in_force,
              a.stop_price, a.order_date, if( b.filled_amount is null, 0, b.filled_amount ) filled
              from orderbook a
              left join (select sum(a_amount) filled_amount, a_order_id from order_transaction group by a_order_id) b
              on a.order_id = b.a_order_id
              where a.order_status='open' and a.offer_asset='$offer_asset' and a.want_asset='$want_asset' and a.customer_id = $customer_id;";
      $data = DB::select($sql);
      return $data;
    }
    public function getFilledOrderList($product) {
      $tmp = explode('-', $product);
      $offer_asset = $tmp[0];
      $want_asset = $tmp[1];
      $customer_id = 1;
      $sql = "SELECT ob.order_id,ot.a_amount size,ot.trade_price price, ot.a_commission*ot.trade_price fee,
      ot.updated_at time, ob.want_asset, ob.offer_asset, concat(ob.offer_asset, '-', ob.want_asset) product, ob.order_side, ob.order_type FROM `order_transaction` ot
            join orderbook ob
            on ot.a_order_id = ob.order_id
            where ob.customer_id = $customer_id and ot.offer_asset='$offer_asset' and ot.want_asset = '$want_asset'";
      $data = DB::select($sql);
      return $data;
    }

    public function getOrderBookList($customer_id, $offer_asset, $want_asset, $order_side, $aggregation) {
      $sql = "select ob.order_id ,  ob.price, ob.quantity size, if( ot.filled_size is null, 0, ot.filled_size) filled_size, if( c.b_amount is null, '-', c.b_amount) my_size, ob.order_date
              from orderbook ob
              left join (select sum(a_amount) filled_size, a_order_id 
              from order_transaction 
              where offer_asset='$offer_asset' and want_asset='$want_asset'
              group by a_order_id) ot
              on ob.order_id = ot.a_order_id
              left join (select sum(a.b_amount) b_amount, a.b_order_id
              from order_transaction a
              join orderbook b
              on a.b_order_id=b.order_id
              where b.customer_id=$customer_id and b.offer_asset = '$offer_asset' and b.want_asset='$want_asset'
              group by a.b_order_id) c
              on c.b_order_id=ob.order_id
              where ob.order_status = 'open' and ob.order_type in ('limit', 'stop', 'market') and ob.order_side='$order_side' and ob.offer_asset='$offer_asset' and ob.want_asset='$want_asset' and ob.customer_id<>$customer_id and (ob.price mod 10) mod $aggregation=0
              order by order_date desc
              limit 0,50;
              ";
              // echo $sql;exit;
      $data = DB::select($sql);
      return $data;
    }
}
