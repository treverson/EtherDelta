@extends('layouts.app')

@section('content')
<div class="container" style="margin-top:50px;width:100%;">
    <div class="row">
        <div class="col-md-2">
            <a class="btn btn-primary" href="/" >Home</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline">
                <div class="form-group">
                    <label for="want_asset">Want Asset:</label>
                    <select class="form-control" id="want_asset">
                        <option value="BTC">BTC</option> 
                        <option value="ETH">ETH</option>
                        <option value="LTC">LTC</option>
                        <option value="DASH">DASH</option>
                        <option value="XRP">RIPPLE</option>
                        <option value="XMR">MONERO</option>
                        <option value="ZEC">ZCASH</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="offer_asset">Offer Asset:</label>
                    <select class="form-control" id="offer_asset">
                        <option value="BTC">BTC</option> 
                        <option value="ETH">ETH</option>
                        <option value="USDT">USDT</option>
                    </select>
                </div>
                <div class="form-group">
                    <input class="form-control text-right" type="number" id="bottom" placeholder="0.001" required/>
                    <label for="bottom"><=</label>
                </div>
                <div class="form-group">
                <label for="top">Fee<=</label>
                    <input class="form-control text-right" type="number" id="top" placeholder="0.001" required/>
                </div>
                <div class="form-group">
                    <label for="fee">Fee:</label>
                    <input class="form-control text-right" type="number" id="fee" placeholder="0.001%" required/>
                </div>
                <div class="form-group">
                    <label for="rebate">Rebate:</label>
                    <input class="form-control text-right" type="number" id="rebate" placeholder="0.001%"/>
                </div>
                <button type="button" class="btn btn-success" id="btn_save">Save</a>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Range</th>
                    <th>Fee</th>
                    <th>Rebate</th>
                    <th>Updated Date</th>
                    <th>Created Date</th>
                </tr>
                </thead>
                <tbody id="asset_fee_data">
                
                </tbody>
            </table>
        </div>
    </div>
</div>
<script  src="{{ URL::asset('/js/feemng/feemng.js') }}"></script>
@endsection