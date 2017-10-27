<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeFee extends Model
{
    //
    protected $table = 'tbl_trade_fee';
    public function saveData( $arr ) {
        $data = $this->where('want_asset', $arr['want_asset'])
                     ->where('offer_asset', $arr['offer_asset'])
                     ->where('bottom', $arr['bottom'])
                     ->where('top', $arr['top'])->get()->count();
                    
        if ( $data === 0 ) {
            $arr['created_at'] = date('Y-m-d H:i:s');
            $arr['updated_at'] = date('Y-m-d H:i:s');
            $this->insert($arr);
            return 'insert';
        }        
        $arr['updated_at'] = date('Y-m-d H:i:s');
        $this->where('want_asset', $arr['want_asset'])
             ->where('offer_asset', $arr['offer_asset'])
             ->where('bottom', $arr['bottom'])
             ->where('top', $arr['top'])
             ->update($arr);
        return 'update';
    } 
    public function getAssetFeeData( $want_asset, $offer_asset ) {
        $data = $this->where('want_asset', $want_asset)->where('offer_asset', $offer_asset)->orderBy('bottom', 'asc')->get()->toArray();
        return $data;
    }
}
