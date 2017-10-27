<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderTransaction;
use DB;

class OrderBook extends Model
{
    //
    protected $table = 'order_book';
    // protected $table = 'tbl_orderbook';

    public function findRowById($orderbookId) {
      $row = $this->where('id', '=', $orderbookId)->first()->toArray();
      return $row;
    }
    public function getOrderBookList($product) {
      //
      $buy_data = $this->where('order_type', '<>', 'stop')->whereNotIn('order_status', array('cancelled', 'closed'))->where('order_action','=','buy')->where('product', '=', $product)->get()->toArray();
      $sell_data = $this->where('order_type', '<>', 'stop')->whereNotIn('order_status', array('cancelled', 'closed'))->where('order_action','=','sell')->where('product', '=', $product)->get()->toArray();
      return array('buy'=>$buy_data, 'sell'=>$sell_data);
    }
    public function getPriceTimePriorityData( $id, $price, $time, $order_action ) {

      $data = $this->where('id', '<', $id)->where('order_action', '<>', $order_action)
                   ->orderBy('trade_price', 'asc')->orderBy('created_at', 'desc')->get()->toArray();
      return $data;
    }
    public function getOrderBookForLimit($order, $trade_price) {

      if ( $order['order_action'] == 'buy' ) {
        $data = $this->where('id', '<', $order['id'])->where('order_action', '<>', $order['order_action'])
                     ->where('limit_price', '>=', $order['limit_price'])->whereNotNull('limit_price')
                     ->orderBy('trade_price', 'desc')->orderBy('created_at', 'desc');
      }
      else {
        $data = $this->where('id', '<', $order['id'])->where('order_action', '<>', $order['order_action'])
                     ->where('limit_price', '<=', $order['limit_price'])->whereNotNull('limit_price')
                     ->orderBy('trade_price', 'asc')->orderBy('created_at', 'desc');
      }
      return $data->get()->toArray();
    }
    public function getFilledDataOfOrder($product, $order_id = null) {
      $ret_arr = array();
      $orderTransactionModel = new OrderTransaction();
      if ( is_null($order_id) ) {
        $ret_arr = DB::select(DB::raw("select if( (ob.amount-b.amount) is null, 0, (ob.amount-b.amount)) filling_amount, if( b.amount is null, 0, b.amount) filled_amount,
        ob.*
        from order_book ob
        left join (SELECT a_order_id, sum(a_amount) amount FROM order_transaction group by a_order_id) as b
        on ob.id = b.a_order_id
        where ob.product='$product';"));

      }
      else {
        $row = $this->where('id', '=', $order_id)->first()->toArray();
        $filled_amount = $orderTransactionModel->getFilledAmountA($order_id);
        $row['fillled_amount'] = $row['amount'] - $filled_amount;
        $ret_arr[] = $row;
      }
      return $ret_arr;
    }
    public function updateOrderStatus( $order_id, $orderStatus ) {
      $this->where('id', $order_id)->update(['order_status'=>$orderStatus, 'updated_at'=>date('Y-m-d H:i:s')]);
    }
}
