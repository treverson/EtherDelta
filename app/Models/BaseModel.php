<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use DB;
use File;

class BaseModel extends Model
{
    //
    protected $table;
    protected $primaryKey;

    private $MySQLBuilder;
    
    public function __construct() {
        
    }
    public function setTableName( $_table ) {
        $this->setTable($_table);
    }
    public function getTableName() {
        return $this->table;
    }
    public function setPrimaryKey($_primaryKey) {
        $this->setKeyName($_primaryKey);
    }
    public function getPrimarykey() {
        return $this->primaryKey;
    }
    public function hasTable($tablename) {
        return Schema::hasTable($tablename);
    }
    public function createNewTable( $origin_table_name, $year=null, $month=null ) {
        if ( is_null($year) ) $year = date("Y");
        if ( is_null($month) ) $month = date("m");
        $new_table_name = $origin_table_name.'_'.$year.'_'.$month;

        $sql = "SHOW CREATE TABLE `$origin_table_name`";
        $tmp = DB::select($sql);
        $createTable = $this->stdToArray($tmp[0]);
        $sql_content = $createTable['Create Table'];
        $sql_content = str_replace($origin_table_name, $new_table_name, $sql_content);
        try{
            if ($this->hasTable($new_table_name)) return false;
            DB::connection()->getPdo()->exec($sql_content);
            return true;
        }
        catch(Execption $exp) {
            return false;
        }        
    }
    public function getDataonCondition(  ) {

    }
    public function insertDatafromOldTable($data_arr) {
        $this->insert($data_arr);
    }
    public function stdToArray($stdObj) {
        $ret_arr = array();
        foreach( $stdObj as $key=>$val ) {
            $ret_arr[$key] = $val;
        }
        return $ret_arr;
    }
}
