<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common;

class AssetDeposit extends Model
{
    //
    protected $table = 'tbl_asset_deposit';
    public function saveAssetDeposit($arr) {
        date_default_timezone_set("UTC");
        foreach( $arr as $row_arr ) {
            $row_arr['created_at'] = date('Y-m-d H:i:s');
            $row_arr['updated_at'] = date('Y-m-d H:i:s');
            $this->insert($row_arr);
        }
    
    }
}
