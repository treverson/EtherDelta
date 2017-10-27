<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use DB;

class OrderBookModel extends BaseModel 
{
    //
    private $_origin_table_name = 'orderbook';
    private $_primary_key = 'order_id';

    public function __construct( $year=null, $month=null ) {
        if ( is_null($year) ) $year = date('Y');
        if ( is_null($month) ) $month = date('m');
        $newTableName = $this->_origin_table_name.'_'.$year.'_'.$month;
        if ( !$this->hasTable($newTableName) )
            $this->createNewTable($this->_origin_table_name, $year, $month);
        $this->setTableName($newTableName);
        $this->setPrimarykey($this->_primary_key);
    }
    public function getPendingData($order_type=null) {

    }
    /**
        ** Store new order to OrderBook
        ****   $sql = "insert into `orderbook_2017_09` select * from orderbook where order_status in ('pending', 'open')"
    */
    public function storeNewOrder( $new_order_data ) {
        $this->insert( $new_order_data );
    }
    /***
        ** $price - trading price
    */
    public function PlaceBuyBid($customer_id, $price, $expiration_date) {
        $tbName = $this->getTableName();
        $buy_data = DB::table($tbName)->where('order_side', 'buy')/*->where('customer_id', '<>',$customer_id)*/->where('order_status', 'pending')->get()->toArray();
        $sqls = array();
        $sqlString = "";
        if ($buy_data) {
            foreach( $buy_data as $record ) {
                if ( $record->order_type == 'limit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->limit_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stop' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stoplimit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
            }
            $sqlString .= implode(';', $sqls);
        }
        if (!empty($sqlString))
            DB::connection()->getPdo()->exec($sqlString);
    }
    public function PlaceSellAsk($customer_id, $price, $expiration_date) {
        $tbName = $this->getTableName();
        $buy_data = DB::table($tbName)->where('order_side', 'sell')/*->where('customer_id', '<>',$customer_id)*/->where('order_status', 'pending')->get()->toArray();
        $sqls = array();
        $sqlString = "";
        if ($buy_data) {
            foreach( $buy_data as $record ) {
                if ( $record->order_type == 'limit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->limit_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stop' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stoplimit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
            }
            $sqlString = implode(';', $sqls);
        }
        if (!empty($sqlString))
            DB::connection()->getPdo()->exec($sqlString);
    }
    public function getOpenOrderData($customer_id, $want_asset, $offer_asset) {
        $orderTransModel = new OrderTransaction();
        $ordertransaction_table = $orderTransModel->getTable();
        $orderbook_table = $this->getTableName();
        $sql = "select
        a.quantity size, a.price, 0 fee, a.updated_at time, a.order_status status, a.order_side,
        a.order_type, a.expiration_date, a.order_id, a.want_asset, a.offer_asset, a.customer_id, a.time_in_force,
        a.stop_price, a.order_date, if( b.filled_amount is null, 0, b.filled_amount ) filled
        from `$orderbook_table` a
        left join (select sum(a_amount) filled_amount, a_order_id from $ordertransaction_table group by a_order_id) b
        on a.order_id = b.a_order_id
        where a.order_status IN ('open', 'pending','rejected') and a.offer_asset='$offer_asset' and a.want_asset='$want_asset' and a.customer_id = $customer_id;";
        $data = DB::select($sql);
        return $data;
    }
    public function getFilledOrderList( $customer_id, $want_asset, $offer_asset ) {
        $orderTransModel = new OrderTransaction();
        $ordertransaction_table = $orderTransModel->getTable();
        $orderbook_table = $this->getTableName();
        $sql = "SELECT ob.order_id,ot.a_amount size,ot.trade_price price, ot.a_commission*ot.trade_price fee,
        ot.updated_at time, ob.want_asset, ob.offer_asset, concat(ob.offer_asset, '-', ob.want_asset) product, ob.order_side, ob.order_type FROM `$ordertransaction_table` ot
              join `$orderbook_table` ob
              on ot.a_order_id = ob.order_id
              where ob.customer_id = $customer_id and ot.offer_asset='$offer_asset' and ot.want_asset = '$want_asset'";
        $data = DB::select($sql);
        return $data;
    }
    public function getOrderBookList($customer_id, $want_asset, $offer_asset, $order_side, $aggregation) {
        $orderTransModel = new OrderTransaction();
        $ordertransaction_table = $orderTransModel->getTable();
        $orderbook_table = $this->getTableName();
        $sql = "select ob.order_id ,  ob.price, ob.limit_price, ob.stop_price, ob.quantity size, if( ot.filled_size is null, 0, ot.filled_size) filled_size, if( c.b_amount is null, '-', c.b_amount) my_size, ob.order_date
                from `$orderbook_table` ob
                left join (select sum(a_amount) filled_size, a_order_id 
                from `$ordertransaction_table` 
                where offer_asset='$offer_asset' and want_asset='$want_asset'
                group by a_order_id) ot
                on ob.order_id = ot.a_order_id
                left join (select sum(a.b_amount) b_amount, a.b_order_id
                from `$ordertransaction_table` a
                join `$orderbook_table` b
                on a.b_order_id=b.order_id
                where b.customer_id=$customer_id and b.offer_asset = '$offer_asset' and b.want_asset='$want_asset'
                group by a.b_order_id) c
                on c.b_order_id=ob.order_id
                where ob.order_status = 'open' and ob.order_type in ('limit', 'stop', 'market') and ob.order_side='$order_side' and ob.offer_asset='$offer_asset' and ob.want_asset='$want_asset' and ob.customer_id<>$customer_id /* and (ob.price mod 10) mod $aggregation=0 */
                order by order_date desc
                limit 0,50;
                ";
                // echo $sql;exit;
        $data = DB::select($sql);
        return $data;
    }
    public function OrderMatch($order) {
        date_default_timezone_set("UTC");
        $quantity = $init_q = $order['quantity'];
        $matchOrders = $this->getMatchingDataForOrder($order);
        $maxmin_price_data = $this->getTradeMaxMinOpenPrice( $order );
        $assetModel = new AssetBalance();
        $tradeHistoryModel = new TradePriceHistory();

        $orderTransModel = new OrderTransaction();        
        $trade_price = Common::getTradePrice($order['want_asset'], $order['offer_asset']);
        $open_price = 0;
        if ( $matchOrders ) {
            $orderTrans_arr = array();
            
            $matchorder = Common::std_to_array($matchOrders[0]);
            $open_price = $matchorder['price'];
            $fee = 0.0001;  // 0.1%
            $match_order_filled_amount = $orderTransModel->getFilledAmountB($matchorder['order_id']);
            $quantity_filled_amount = $matchorder['quantity']-$match_order_filled_amount;

            if ( $order['order_type']=='limit' || $order['order_type']=='stoplimit' ) {
                if ( $order['time_in_force'] == 'FOK' ) {
                    if ( $quantity == $quantity_filled_amount ) {
                        DB::table($this->getTableName())->where('order_id', '=', $order['order_id'])->update(['order_status'=>'closed']);  /// ?

                        // TODO - Trade Price history register
                        
                        ($order['order_type'] == 'limit' || $order['order_type'] == 'stoplimit') ? $_tp = $order['price'] : $_tp = $trade_price;
                        $trade_history_data = array('id' => time('Y-m-d\TH:i:s\Z'),'open_price'=>$open_price, 'close_price'=>$_tp, 'high_price'=>$maxmin_price_data->high, 'low_price'=>$maxmin_price_data->low, 'volume'=>$quantity_filled_amount, 'updated_at'=>date('Y-m-d H:i:s'), 'created_at'=>date('Y-m-d H:i:s'));
                        $tradeHistoryModel->InsertNewTradePrice($trade_history_data);
                        /////////
                        $ret_arr['a_amount'] = $quantity_filled_amount;
                        $ret_arr['b_amount'] = $quantity_filled_amount;
                        $ret_arr['a_commission'] = $fee;
                        $ret_arr['a_order_id'] = $order['order_id'];
                        $ret_arr['b_order_id'] = $matchorder['order_id'];
                        $ret_arr['offer_asset'] = $order['offer_asset'];
                        $ret_arr['want_asset'] = $order['want_asset'];
                        $ret_arr['trade_price'] = $order['price'];
                        $orderTrans_arr[] = $ret_arr;

                        

                        //Customer Asset balance Reset
                        $assetModel->releaseCustomerAssetBalance($order['customer_id'], $order['order_side'], $matchorder['customer_id'], $quantity_filled_amount, $order['price'], $order['want_asset'], $order['offer_asset'], $fee);

                        $orderTransModel->addOrderTransaction($orderTrans_arr);
                    }
                    else
                        DB::table($this->getTableName())->where('order_id', '=', $order['order_id'])->update(['order_status'=>'rejected']);
                    return;
                }
            }

            foreach( $matchOrders as $m_order ) {
                $match_order = Common::std_to_array($m_order);
                $fee = 0;
                $match_order_filled_amount = $orderTransModel->getFilledAmountB($match_order['order_id']);
                $quantity_filled_amount = $match_order['quantity']-$match_order_filled_amount;
                if ( $quantity <= 0 ) {
                    DB::table($this->getTableName())->where('order_id', '=', $order['order_id'])->update(['order_status'=>'closed']);  /// ?
                    // TODO - Trade Price history register
                    ($order['order_type'] == 'limit' || $order['order_type'] == 'stoplimit') ? $_tp = $order['limit_price'] : $_tp = $trade_price;
                    $trade_history_data = array('id' => time('Y-m-d\TH:i:s\Z'),'open_price'=>$open_price, 'close_price'=>$_tp, 'high_price'=>$maxmin_price_data->high, 'low_price'=>$maxmin_price_data->low, 'volume'=>$init_q, 'updated_at'=>date('Y-m-d H:i:s'), 'created_at'=>date('Y-m-d H:i:s'));
                    $tradeHistoryModel->InsertNewTradePrice($trade_history_data);
                    /////////
                    break;
                }
                if ($quantity_filled_amount <= 0) continue;
                if ( $quantity_filled_amount < $quantity ) {
                    $ret_arr['a_amount'] = $quantity_filled_amount;
                    $ret_arr['b_amount'] = $quantity_filled_amount;
                    DB::table($this->getTableName())->where('order_id', '=', $match_order['order_id'])->update(['order_status'=>'closed']);
                }
                else {
                    $ret_arr['a_amount'] = $quantity;
                    $ret_arr['b_amount'] = $quantity;
                }
                $ret_arr['a_commission'] = $fee;
                $ret_arr['a_order_id'] = $order['order_id'];
                $ret_arr['b_order_id'] = $match_order['order_id'];
                $ret_arr['offer_asset'] = $order['offer_asset'];
                $ret_arr['want_asset'] = $order['want_asset'];
                if ($order['order_type']=='limit' || $order['order_type']=='stoplimit') {
                    $ret_arr['trade_price'] = $order['price'];
                    //Customer Asset balance Reset
                    $assetModel->releaseCustomerAssetBalance($order['customer_id'], $order['order_side'], $match_order['customer_id'], $quantity_filled_amount, $order['price'], $order['want_asset'], $order['offer_asset'], $fee);
                }
                elseif ($order['order_type']=='stop'||$order['order_type']=='market') {
                    # code...
                    // if ( isset($order['stop_price']) && !isset($order['price']) )
                        $ret_arr['trade_price'] = $trade_price;
                        //Customer Asset balance Reset
                        $assetModel->releaseCustomerAssetBalance($order['customer_id'], $order['order_side'], $matchorder['customer_id'], $quantity_filled_amount, $trade_price, $order['want_asset'], $order['offer_asset'], $fee);
                    // else if (isset($order['stop_price']) && isset($order['price']))
                    //     $ret_arr['trade_price'] = $order['stop_price'];
                }
                // else {
                //     $ret_arr['trade_price'] = $match_order['price'];
                // }
                $orderTransModel->addOrderTransaction($ret_arr);
                $orderTrans_arr[] = $ret_arr;
                $quantity -= $quantity_filled_amount;
                // $order_trade_arr[] = $ret_arr;
            }
            if ( $order['order_type']=='limit' || $order['order_type']=='stoplimit' ) {
                if ( $order['time_in_force'] == 'IOC' && $quantity > 0 ) {
                    DB::table($this->getTableName())->where('order_id', '=', $order['order_id'])->update(['order_status'=>'closed', 'quantity'=>($init_q - $quantity)]);  /// ?
                    // TODO - Trade Price history register
                    ($order['order_type'] == 'limit' || $order['order_type'] == 'stoplimit') ? $_tp = $order['limit_price'] : $_tp = $trade_price;
                    $trade_history_data = array('id' => time('Y-m-d\TH:i:s\Z'),'open_price'=>$open_price, 'close_price'=>$_tp, 'high_price'=>$maxmin_price_data->high, 'low_price'=>$maxmin_price_data->low, 'volume'=>($init_q - $quantity), 'updated_at'=>date('Y-m-d H:i:s'), 'created_at'=>date('Y-m-d H:i:s'));
                    $tradeHistoryModel->InsertNewTradePrice($trade_history_data);
                    /////////
                }
            }
            // if ( count($orderTrans_arr) == 1 ) {
            //     $orderTransModel->addOrderTransaction($orderTrans_arr[0]);
            // }
            // else {
            //     $orderTransModel->addOrderTransaction($orderTrans_arr);
            // }
            
        }
        else {
            DB::table($this->getTableName())->where('order_id', $order['order_id'])->update(['order_status'=>'pending', 'updated_at'=>date('Y-m-d H:i:s')]);
        }
        return;
    }
    public function getMatchingDataForOrder( $order ) {
        $tbName = $this->getTableName();
        if ($order['order_side']=='buy') {
            if ( $order['order_type'] == 'market' ) {
                $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->orderBy('price', 'desc')->orderBy('updated_at', 'asc')->get()->toArray();
            }
            else {
                $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->where('price', '<=',$order['price'])->orderBy('price', 'desc')->orderBy('updated_at', 'asc')->get()->toArray();
            }            
        }
        else {
            if ( $order['order_type'] == 'market' ) {
                $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->orderBy('price', 'desc')->orderBy('updated_at', 'asc')->get()->toArray();
            }
            else {
                $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->where('price', '>=',$order['price'])->orderBy('price', 'desc')->orderBy('updated_at', 'asc')->get()->toArray();
            }
        }
        return $data;
    }
    /**
    **
        ** return param stdObj{ high, low }
    */
    public function getTradeMaxMinOpenPrice( $order ) {
        $tbName = $this->getTableName();
        if ($order['order_side']=='buy') {
            $data = DB::table($tbName)->select(DB::raw('MAX(price) high, MIN(price) low'))->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->where('price', '<=',$order['price'])->get()->first();
        }
        else {
            $data = DB::table($tbName)->select(DB::raw('MAX(price) high, MIN(price) low'))->where('customer_id', '<>', $order['customer_id'])->where('order_side','<>', $order['order_side'])->where('order_status', '=', 'open')->where('price', '>=', $order['price'])->get()->first();
        }
        return $data;
    }
    public function getData() {
        return $this->where('order_type', 'limit')->get();
    }
}











