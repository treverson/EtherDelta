<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradeFee;

class FeemngController extends Controller
{
    //
    public function index() {
        return view('feemng.index');
    }

    public function FeeSave() {
        $arr = array();
        $arr['want_asset'] = request()->get('want_asset');
        $arr['offer_asset'] = request()->get('offer_asset');
        $arr['bottom'] = request()->get('bottom');
        $arr['top'] = request()->get('top');
        $arr['fee'] = request()->get('fee');
        $arr['rebate'] = request()->get('rebate');

        $model = new TradeFee();
        $saveRet = $model->saveData($arr);        
        if ( $saveRet != 'insert' && $saveRet != 'update' ) 
            echo 'FAIL';
        else {
            echo 'SUCCESS';
        }
    }
    public function getAssetFeeData($product) {
        header('Content-type:application/json');
        $arr = explode('-', $product);
        $model = new TradeFee();
        echo json_encode($model->getAssetFeeData($arr[0], $arr[1]));
    }
}
