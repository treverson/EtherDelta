<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBalance extends Model
{
    //
    protected $table = 'tbl_asset_balance';

    public function resetBalance( $action, $asset_rows ) {
        foreach( $asset_rows as $asset_row ) {
            $data = $this->where('customer_id', $asset_row['customer_id'])->where('asset', $asset_row['asset'])->first();
            $new_row = array();
            if ( is_null($data) ) {
                if ( $action != 'deposit' ) continue;
                $new_row['customer_id'] = $asset_row['customer_id'];
                $new_row['asset'] = $asset_row['asset'];
                $new_row['balance'] = $asset_row['deposit_amount'];
                $new_row['created_at'] = date('Y-m-d H:i:s');
                $new_row['updated_at'] = date('Y-m-d H:i:s');
                $this->insert($new_row);
            }
            else {
                if ( $action == 'deposit' )
                    $new_row['balance'] = $data->balance*1 + $asset_row['deposit_amount'];
                else
                    $new_row['balance'] = $data->balance*1 - $asset_row['withdrawal_amount'];
                $new_row['updated_at'] = date('Y-m-d H:i:s');
                $this->where('customer_id', $asset_row['customer_id'])->where('asset', $asset_row['asset'])->update($new_row);
            }
        }
    }
    public function getCustomerAssetBalance( $customer_id, $asset ) {
        $data = $this->where('customer_id', $customer_id)->where('asset', $asset)->first();
        if ( is_null($data) ) return 0;
        return $data->balance;
    }
    public function getCustomerAssetBalanceInfo( $customer_id, $asset ) {
        $data = $this->where('customer_id', $customer_id)->where('asset', $asset)->first();
        if ( is_null($data) ) return array('asset_balance'=>0, 'frozen_balance'=>0);
        return array('asset_balance'=>$data->balance, 'frozen_balance'=>$data->frozen_balance);
    }
    public function setFrozenBalance( $customer_id, $asset, $hold_balance ) {
        $data = $this->where('customer_id', $customer_id)->where('asset', $asset)->first();
        $frozen_balance = $data->frozen_balance + $hold_balance;
        $this->where('customer_id', $customer_id)->where('asset', $asset)->update(['frozen_balance'=>$frozen_balance]);
    }
    public function releaseCustomerAssetBalance( $a_customer_id, $a_order_side, $b_customer_id, $want_asset_quantity, $trade_price, $want_asset, $offer_asset, $fee ) {
        $data = $this->where('customer_id', $a_customer_id)->where('asset', $want_asset)->first();
        $a_want_balance = $data->balance;
        $a_want_frozen_balance = $data->frozen_balance;
        $data = $this->where('customer_id', $a_customer_id)->where('asset', $offer_asset)->first();
        $a_offer_balance = $data->balance;
        $a_offer_frozen_balance = $data->frozen_balance;

        $data = $this->where('customer_id', $b_customer_id)->where('asset', $want_asset)->first();
        $b_want_balance = $data->balance;
        $b_want_frozen_balance = $data->frozen_balance;
        $data = $this->where('customer_id', $b_customer_id)->where('asset', $offer_asset)->first();
        $b_offer_balance = $data->balance;
        $b_offer_frozen_balance = $data->frozen_balance;
        if ( $a_order_side == 'buy' ) {
            // A_customer_side
            $a_want_balance += $want_asset_quantity;
            $a_offer_balance -= (1+$fee)*$want_asset_quantity*$trade_price;
            $a_offer_frozen_balance -= (1+$fee)*$want_asset_quantity*$trade_price;

            // B_customer_side
            $b_want_balance -= $want_asset_quantity;
            $a_offer_balance += (1-$fee)*$want_asset_quantity*$trade_price;
            $a_want_frozen_balance -= $want_asset_quantity;
        }
        else {
            // A_customer_side
            $a_want_balance -= $want_asset_quantity;
            $a_want_frozen_balance -= $want_asset_quantity;
            $a_offer_balance += (1-$fee)*$want_asset_quantity*$trade_price;

            // B_customer_side
            $b_want_balance += $want_asset_quantity;
            $a_offer_balance -= (1+$fee)*$want_asset_quantity*$trade_price;
            $a_offer_frozen_balance -= (1+$fee)*$want_asset_quantity*$trade_price;
        }
        $this->where('customer_id', $a_customer_id)->where('asset', $want_asset)->update(['balance'=>$a_want_balance, 'frozen_balance'=>$a_want_frozen_balance]);
        $this->where('customer_id', $a_customer_id)->where('asset', $offer_asset)->update(['balance'=>$a_offer_balance, 'frozen_balance'=>$a_offer_frozen_balance]);

        $this->where('customer_id', $b_customer_id)->where('asset', $want_asset)->update(['balance'=>$b_want_balance, 'frozen_balance'=>$b_want_frozen_balance]);
        $this->where('customer_id', $b_customer_id)->where('asset', $offer_asset)->update(['balance'=>$b_offer_balance, 'frozen_balance'=>$b_offer_frozen_balance]);
    }
}
