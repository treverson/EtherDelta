<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderBook;
use App\Models\TempModel;
use App\Models\TradePriceHistory;
use App\Models\OrderTransaction;
use App\Models\Common;
use App\Models\OrderBookList;
use App\Models\AssetBalance;
use App\Models\AssetDeposit;
use App\Models\AssetWithdraw;
use App\Models\OrderBookModel;
use DB;

class OrderController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
      return view('order.index');
    }



    public function addOrder() {
      date_default_timezone_set("UTC");
      $order_data = array();
      $order_data['customer_id'] = \Auth::user()->id;
      $order_data['order_side'] = request()->get('order_side');
      $order_data['order_type'] = request()->get('order_type');
      $order_data['quantity'] = request()->get('quantity');
      $order_data['price'] = request()->get('price');
      $order_data['limit_price'] = floatval(request()->get('limit_price'));
      $order_data['stop_price'] = floatval(request()->get('stop_price'));
      $order_data['offer_asset'] = request()->get('offer_asset');
      $order_data['want_asset'] = request()->get('want_asset');
      if ( $order_data['order_type'] =='limit' || $order_data['order_type'] == 'stoplimit' ) {
        $order_data['price'] = $order_data['limit_price'];
      }

      if (request()->get('expiration_date') != 'NONE' && request()->get('expiration_date') !=1) $order_data['expiration_date'] = request()->get('expiration_date');
      if ( request()->get('expiration_date') == 1 )  $order_data['expiration_date'] = date("Y-m-d 23:59:59");
      if (request()->get('time_in_force') != 'NONE') $order_data['time_in_force'] = request()->get('time_in_force');
      if ( $order_data['order_type'] == 'market' || ( $order_data['order_type']=='limit' && $order_data['time_in_force'] == 'IOC') )
        $order_data['order_status'] = 'open';
      else
        $order_data['order_status'] = 'pending';
      $order_data['order_date'] = Common::udate('Y-m-d H:i:s.u');
      $order_data['created_at'] = date('Y-m-d H:i:s');
      $order_data['updated_at'] = date('Y-m-d H:i:s');
      
      $order_data['order_id'] = time('Y-m-d\TH:i:s\Z');

      // $orderTransModel = new OrderTransaction();
      $trade_price = Common::getTradePrice($order_data['want_asset'], $order_data['offer_asset']);

      $assetModel = new AssetBalance();
      if ( $order_data['order_side'] == 'buy' ) {
        $assetData = $assetModel->getCustomerAssetBalanceInfo($order_data['customer_id'], $order_data['offer_asset']);
        $assetBalance = $assetData['asset_balance']-$assetData['frozen_balance'];
     
        if ( $assetBalance<$order_data['quantity']*$order_data['price'] ) {
          $order_data['order_status'] = 'rejected';
        }
      }
      else {
        $assetData = $assetModel->getCustomerAssetBalanceInfo($order_data['customer_id'], $order_data['want_asset']);
        $assetBalance = $assetData['asset_balance']-$assetData['frozen_balance'];
        if ( $assetBalance<$order_data['quantity'] ) {
          $order_data['order_status'] = 'rejected';
        }
      }


      $orderModel = new OrderBookModel();
      $orderModel->getTradeMaxMinOpenPrice($order_data);
      $orderModel->storeNewOrder($order_data);
      $fee = 0.0001;  // 0.0001%;
      if ( $order_data['order_side'] == 'buy' ) {
        $orderModel->PlaceBuyBid($order_data['customer_id'], $trade_price, date('Y-m-d H:i:s'));
        $frozenBalance = (1+$fee)*$trade_price*$order_data['quantity'];
        $assetModel->setFrozenBalance($order_data['customer_id'], $order_data['offer_asset'], $frozenBalance);
      }
      else {
        $orderModel->PlaceSellAsk($order_data['customer_id'], $trade_price, date('Y-m-d H:i:s'));
        $assetModel->setFrozenBalance($order_data['customer_id'], $order_data['offer_asset'], $order_data['quantity']);
      }

      $ret = $orderModel->OrderMatch($order_data);
var_dump(print_r($ret, true));
      exit;
      $order_date = Common::udate('Y-m-d H:i:s.u');

      

      $orderModel->order_id = $order_id;
      $orderModel->customer_id = $customer_id;
      $orderModel->order_side = $order_side;
      $orderModel->offer_asset = $offer_asset;
      $orderModel->want_asset = $want_asset;
      $orderModel->quantity = $quantity;
      $orderModel->price = $price;
      $orderModel->order_type = $order_type;
      $orderModel->order_status = 'pending';
      $orderModel->order_date = $order_date;
      $orderModel->stop_price = $stop_price;
      $orderModel->save();

      //  Place
      $orderModel->setOrder( $order_id );
      $trade_price = Common::getTradePrice($want_asset, $offer_asset);
      // $orderTransModel = new OrderTransaction();
      // $trade_price = $orderTransModel->getTradePrice($offer_asset, $want_asset);
      if ( $order_side == 'buy' ) {
        if ($orderModel->getPlaceBuyBid($order_id)) {
          if ( $order_type == 'limit' ) {  // if current order is limit order, process with $price
            if ( $price >= $trade_price ) {
              $matchingData = $orderModel->getOrderListForBuy($price, $customer_id, 'BTC', 'USD');
              //dd($matchingData);
              if ( $matchingData ) {
                $orderModel->matchOrders( $matchingData );
              }
            }
          }
          elseif( $order_type == 'market' ) {  // if current order is market, process with $trade_price
            $matchingData = $orderModel->getOrderListForBuy($trade_price, $customer_id, 'BTC', 'USD');
            //dd($matchingData);
            if ( $matchingData ) {
              $orderModel->matchOrders( $matchingData );
            }
          }
          else {
            if ( isset($stop_price) && !isset($price) ) {  // if current order is stop order,
              $matchingData = $orderModel->getOrderListForBuy($trade_price, $customer_id, 'BTC', 'USD');
              //dd($matchingData);
              if ( $matchingData ) {
                $orderModel->matchOrders( $matchingData );
              }
            }
            else if ( isset($stop_price) && isset($price) ) { // if current order is stop limit order,
              $matchingData = $orderModel->getOrderListForBuy($price, $customer_id, 'BTC', 'USD');
              //dd($matchingData);
              if ( $matchingData ) {
                $orderModel->matchOrders( $matchingData );
              }
            }
          }
        }
      }
      else {
        if ($orderModel->getPlaceSellAsk($order_id)) {
          if ( $order_type == 'limit' ) {
            $matchingData = $orderModel->getOrderListForSell($price, $customer_id, 'BTC', 'USD');
            if ( $matchingData ) {
              $orderModel->matchOrders( $matchingData );
            }
          }
          elseif( $order_side == 'market' ) {
            $matchingData = $orderModel->getOrderListForBuy($trade_price, $customer_id, 'BTC', 'USD');
            //dd($matchingData);
            if ( $matchingData ) {
              $orderModel->matchOrders( $matchingData );
            }
          }
          else {

          }
        }
      }


    }

    /**
      * Deposit Action
    */
    public function AssetDeposit($product) {
      $user = \Auth::user();
      $customer_id = $user->id;
      $product_arr = explode('-',$product);
      $want_asset = $product_arr[0];
      $want_asset_amount = request()->get('want_asset_amount');
      $offer_asset = $product_arr[1];
      $offer_asset_amount = request()->get('offer_asset_amount');
      $new_row_data = array();
      if ( floatval($want_asset_amount) != 0 )
        $new_row_data[] = array('customer_id'=>$customer_id, 'asset'=>$want_asset, 'deposit_amount'=>$want_asset_amount);
      if ( floatval($offer_asset_amount) != 0 )
        $new_row_data[] = array('customer_id'=>$customer_id, 'asset'=>$offer_asset, 'deposit_amount'=>$offer_asset_amount);

      $model = new AssetDeposit();
      $model->saveAssetDeposit($new_row_data);

      $balanceModel = new AssetBalance();
      $balanceModel->resetBalance( 'deposit', $new_row_data );
      echo 'ok';
    }
    /**
      * Withdraw Action
    */
    public function AssetWithdraw($product) {
      $user = \Auth::user();
      $customer_id = $user->id;
      $product_arr = explode('-',$product);
      $want_asset = $product_arr[0];
      $want_asset_amount = request()->get('want_asset_amount');
      $offer_asset = $product_arr[1];
      $offer_asset_amount = request()->get('offer_asset_amount');

      $new_row_data = array();
      if ( floatval($want_asset_amount) != 0 )
        $new_row_data[] = array('customer_id'=>$customer_id, 'asset'=>$want_asset, 'withdrawal_amount'=>$want_asset_amount);
      if ( floatval($offer_asset_amount) != 0 )
        $new_row_data[] = array('customer_id'=>$customer_id, 'asset'=>$offer_asset, 'withdrawal_amount'=>$offer_asset_amount);

      $model = new AssetWithdraw();
      $model->saveAssetWithdraw($new_row_data);

      $balanceModel = new AssetBalance();
      $balanceModel->resetBalance( 'withdraw', $new_row_data );
    }

    public function makeFill() {
      $basemodel = new OrderBookModel(2017,'09');
      $data = DB::table('orderbook')->where('order_type', 'limit')->get()->toArray();

      $basemodel->PlaceBuyBid(1, 3600.2, '2017-10-11 22:22;22');
            dd( $data);

      $common = new Common();
      dd(Common::udate('Y-m-d H:i:s.u'));
      dd( $common->udate('Y-m-d H:i:s.u') );
      $id = 46;
      $ret_arr = array();
      $orderModel = new OrderBook();
      $order = $orderModel->findRowById($id);
      $orderTransactionModel = new OrderTransaction();
      //dd($orderTransactionModel->getLastTradePrice());
      $order_sequence = $orderModel->getPriceTimePriorityData($id, $order['trade_price'], date("Y-m-d H;i:s"), 'sell');
//dd($orderModel->getFilledDataOfOrder());
      $tradePriceModel = new TradePriceHistory();
      $tradePrice = $orderTransactionModel->getLastTradePrice(); // $tradePriceModel->getCurrentTradePriceData($order['product']);
// dd($order['order_type'], ($order['order_type'] == "limit"));
      // foreach( $order_sequence as $order ) {
      if ( $order['order_type'] == 'market' ) {
        $this->gotoMarketOrderProcess($order);
      }
      elseif ( $order['order_type'] == "limit" ) {
        $this->gotoLimitOrderProcess($order);
      }
      else {
        $this->gotoStopOrderProcess($order);
      }
      // }

    }

    public function gotoMarketOrderProcess($order) {
      $orderModel = new OrderBook();
      $tradePriceModel = new TradePriceHistory();
      $orderTransaction = new OrderTransaction();
      $tradePrice = $tradePriceModel->getCurrentTradePriceData($order['product']);
    }

    public function gotoLimitOrderProcess($order) {
      $orderModel = new OrderBook();
      $tradePriceModel = new TradePriceHistory();
      $orderTransaction = new OrderTransaction();

      $tradePrice = $orderTransaction->getLastTradePrice(); // $tradePriceModel->getCurrentTradePriceData($order['product']);

      if ( $order['order_action'] == 'buy' && $order['limit_price'] >= $tradePrice ) {
        $order_sequence = $orderModel->getOrderBookForLimit($order, $tradePrice);
        if ( $order_sequence ) {
          $orderTransaction->FillOrdersToMainOrder($order, $order_sequence);
        }
      }
      if ( $order['order_action'] == 'sell' && $order['limit_price'] <= $tradePrice ) {
        $order_sequence = $orderModel->getOrderBookForLimit($order, $tradePrice);
        if ( $order_sequence ) {
          $orderTransaction->FillOrdersToMainOrder($order, $order_sequence);
        }
      }
    }
    public function gotoStopOrderProcess($order) {

    }
    public function getOrderBook() {
      $order_type_arr = array('market', 'limit', 'stop');
      $action_arr = array('buy', 'sell');
      $quantity = rand(1, 30);
      $price = rand(3500, 4500);
      $min_idx = date('i')%3;
      $action_idx = rand(0,3)%2;
      //dd($min_idx,$action_idx);
      $date_time = Common::udate('Y-m-d H:i:s.u');
      // if ( $min_idx == 0 ) {
      //   DB::table('order_book')
      //   ->insert(array('user_email'=>'user1@mail.com', 'product'=>'BTC-USD', 'order_type'=>$order_type_arr[$min_idx], 'order_action'=>$action_arr[$action_idx], 'order_status'=>'pending', 'amount'=>$quantity, 'total_amount'=>$quantity*$price, 'trade_price'=>$price, 'post_only'=>0, 'created_at'=>date("Y-m-d H:i:s"), 'updated_at'=>date("Y-m-d H:i:s")));
      // // $newRow->save();
      // }
      // if ( $min_idx == 1 ) {
      //   for($i=0;$i<10;$i++) {
      //     $tradeHistory = new TradePriceHistory();
      //     $price = $tradeHistory->getCurrentTradePriceData('BTC-USD');
      //     $date_time = Common::udate('Y-m-d H:i:s.u');
      //     DB::table('order_book')
      //     ->insert(array('user_email'=>'user1@mail.com', 'product'=>'BTC-USD', 'order_type'=>'limit', 'order_action'=>$action_arr[$i%2], 'order_status'=>'pending', 'amount'=>$quantity, 'total_amount'=>$quantity*$price, 'trade_price'=>$price, 'limit_price'=>($price+3), 'time_in_force'=>2,'post_only'=>0, 'created_at'=>date("Y-m-d H:i:s"), 'updated_at'=>date("Y-m-d H:i:s")));
      //   // $newRow->save();
      //   }
      // //
      // }
      // if ( $min_idx == 2 ) {
      //   DB::table('order_book')
      //   ->insert(array('user_email'=>'user1@mail.com', 'product'=>'BTC-USD', 'order_type'=>$order_type_arr[$min_idx], 'order_action'=>$action_arr[$action_idx], 'order_status'=>'pending', 'amount'=>$quantity, 'total_amount'=>$quantity*$price, 'trade_price'=>$price, 'limit_price'=>($price+3), 'stop_price'=>($price+3), 'time_in_force'=>2,'post_only'=>0, 'created_at'=>date("Y-m-d H:i:s"), 'updated_at'=>date("Y-m-d H:i:s")));
      // // $newRow->save();
      // }
    }

    /***  REST API part  *****/
    public function getTradePrice($product) {
      $tmp = explode('-', $product);
      header('Content-type:application/json');
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      // $model = new OrderTransaction();
      $ret_data['result']['trade_price'] = Common::getTradePrice($want_asset, $offer_asset);
      echo json_encode($ret_data);
    }

    // public function getOrderBookList($product) {
    //   header('Content-type:application/json');
    //   $orderModel = new OrderBook();
    //   $ret_data = $orderModel->getOrderBookList($product);
    //   echo json_encode($ret_data);
    // }
    /**
      *** Owner Order Data for Open Orders
    */
    public function getOpenOrders($product) {
      header('Content-type:application/json');
      // $orderModel = new OrderBookList();
      $customer_id = \Auth::user()->id;
      $tmp = explode('-', $product);
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      $orderModel = new OrderBookModel();
      $ret_arr = array();
      $ret_arr = $orderModel->getOpenOrderData($customer_id, $want_asset, $offer_asset);
      echo json_encode($ret_arr);
    }
    /**
      *** Owner Filled Data List
    **/
    public function getFillDataOfOrder($product) {
      header('Content-type:application/json');
      $orderModel = new OrderBookList();
      return json_encode($orderModel->getFilledOrderList($product));
    }
    /**
      *** User's filled Order Data List
    **/
    public function getFilledList($product) {
      header('Content-type:application/json');
      $resp_arr = array();
      // $orderModel = new OrderBookList();
      $customer_id = \Auth::user()->id;
      $tmp = explode('-', $product);
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      $orderModel = new OrderBookModel();
      $resp_arr = $orderModel->getFilledOrderList($customer_id, $want_asset, $offer_asset);
      echo json_encode($resp_arr);
    }
    /**
      *** Get Trade History Data List
    **/
    public function getTradeHistory($product) {
      header('Content-type:application/json');
      $tmp = explode('-', $product);
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      $resp_arr = array();
      $model = new OrderTransaction();
      $resp_arr = $model->getTradeHistoryData($offer_asset, $want_asset);
      echo json_encode($resp_arr);
    }
    /**
      *** Get Open Order List
    **/
    public function getOrderBookList($product) {
      header('Content-type:application/json');
      $aggregation = request()->get('aggregation');
      $customer_id = \Auth::user()->id;
      $tmp = explode('-', $product);
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      $resp_arr = array();
      // $orderModel = new OrderBookList();
      $orderModel = new OrderBookModel();
      $resp_arr = array('bid'=>$orderModel->getOrderBookList($customer_id, $want_asset, $offer_asset, 'buy', $aggregation),
                        'ask'=>$orderModel->getOrderBookList($customer_id, $want_asset, $offer_asset, 'sell', $aggregation));
      echo json_encode($resp_arr);
    }

    /***
      *** Get Account Asset Balance
    */
    public function getAssetBalance( $product ) {
      header('Content-type:application/json');
      $user = \Auth::user();
      $customer_id = $user->id;
      $tmp = explode('-', $product);
      $offer_asset = $tmp[1];
      $want_asset = $tmp[0];
      $balanceModel = new AssetBalance();
      $ret_arr = array('want_asset_amount'=>$balanceModel->getCustomerAssetBalance($customer_id, $want_asset),
                       'offer_asset_amount'=>$balanceModel->getCustomerAssetBalance($customer_id, $offer_asset));
      echo json_encode($ret_arr);
    }
}
