<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderTransaction;
use App\Models\BaseModel;
use DB;

class TradePriceHistory extends BaseModel
{
    //
    // protected $table = 'trade_price_history';
    private $_origin_table_name = 'trade_price_history';
    private $_primary_key = 'id';

    public function __construct( $year=null, $month=null ) {
        if ( is_null($year) ) $year = date('Y');
        if ( is_null($month) ) $month = date('m');
        $newTableName = $this->_origin_table_name.'_'.$year.'_'.$month;
        if ( !$this->hasTable($newTableName) )
            $this->createNewTable($this->_origin_table_name, $year, $month);
        $this->setTableName($newTableName);
        $this->setPrimarykey($this->_primary_key);
    }

    public function InsertNewTradePrice( $trade_record ) {
      //DB::table($this->getTableName())
      $this->insert( $trade_record );
    }
    
}
