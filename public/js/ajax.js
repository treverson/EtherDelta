var list_num = 50;
var interval_num_order_book = 0, interval_num_trade = 0, interval_num_open_orders = 0, interval_num_fills = 0;

$(document).ready(function () {
    // var intervalId = window.setInterval(function () {
    //     interval_num_order_book++;
    //     interval_num_trade++;
    //     interval_num_open_orders++;
    //     interval_num_fills++;
    //     getOrderData();
    //     getTradeData();
    //     getOpenOrders();
    //     getFills();
    // }, 3000);
    getOpenOrders();
});

function getOrderData() {
    if (interval_num_order_book == 1)  {
        showLoading('order_book');
    }
    $.get('getorderbooklist/' + front_asset + '-' + back_asset + '?aggregation=' + aggregation_values[aggregation_num], function (resp) {
        var data = typeof resp == 'string' ? JSON.parse(resp) : resp;
        var asks = data.ask;
        var bids = data.bid;

        var asks_bids_tbl_str = '<table style="width:100%;float:left;"><tbody>';
        
        asks_bids_body_html = '';
        var num = asks.length > bids.length ? asks.length : bids.length;
        for (i = 0; i < num; i++){
            
            if (i >= asks.length){
                var ask = {my_size:'&nbsp;', size:'&nbsp;', price:'&nbsp;', size_main:'&nbsp;', size_zero:'&nbsp;'};    
            } else {
                var ask = asks[i];
                ask.size = parseFloat(ask.size).toFixed(8);
                ask.price = parseFloat(ask.price).toFixed(2);
                ask.size_main = ask.size.toString().substr(0, ask.size.toString().length - zero_count(ask.size));
                ask.size_zero = ask.size.toString().substr(ask.size.toString().length - zero_count(ask.size), zero_count(ask.size));
            }
            if (i >= bids.length){
                var bid = {my_size:'&nbsp;', size:'&nbsp;', price:'&nbsp;', size_main:'&nbsp;', size_zero:'&nbsp;'};
            } else {
                var bid = bids[i];
                bid.size = parseFloat(bid.size).toFixed(8);
                bid.price = parseFloat(bid.price).toFixed(2);
                bid.size_main = bid.size.toString().substr(0, bid.size.toString().length - zero_count(bid.size));
                bid.size_zero = bid.size.toString().substr(bid.size.toString().length - zero_count(bid.size), zero_count(bid.size));
            }
            asks_bids_body_html += '<tr class="asks_tr" style="font-size:11px;line-height:16px;" ><td>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">' + ask.my_size + '</span></div>\n\
<div style="text-align:right;width:15%;float:left;"><span style="color:#d7d7d8;">' + ask.size_main + '</span><span style="color:#5c5c5c">' + ask.size_zero + '</span></div>\n\
<div style="text-align:right;width:18%;float:left;"><span style="color:#fd2d2f;">' + ask.price + '</span></div>\n\
<div style="width:4%;float:left;">&nbsp;</div>\n\
<div style="text-align:left;width:18%;float:left;"><span style="color:#31ff31;">' + bid.price + '</span></div>\n\
<div style="text-align:right;width:13%;float:left;padding-right:2%"><span style="color:#d7d7d8;">' + bid.size_main + '</span><span style="color:#5c5c5c">' + bid.size_zero + '</span></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">' + bid.my_size + '</span></div></td></tr>';
        }
        
        asks_bids_tbl_str += asks_bids_body_html + "</tbody></table>";

        $('#div_asks_bids').html(asks_bids_tbl_str);
        if (interval_num_order_book > 0){
            stopLoading('order_book');
        }
    });
}

function getTradeData() {
    if (interval_num_trade == 1)  {
        showLoading('trade');
    }
    $.get('gettradehistory/' + front_asset + '-' + back_asset, function (resp) {
        var trades = typeof resp == 'string' ? JSON.parse(resp) : resp;
        var trade_tbl_str = '<table style="width:100%;float:left;"><tbody>';
        trade_body_html = '';
        for (i = 0; i < trades.length-1; i++) {
            
            var trade = trades[i];
            var tmp = trade.time.split('.')[0].split('T')[0].split('-');
            var time_str = tmp[1] + '-' + tmp[2] + ' ' + trade.time.split('.')[0].split('T')[1];
            if (i == trades.length - 1) {
                nextTrade = trades[i];
            } else {
                nextTrade = trades[i + 1];
            }
            if (trades[i].price > trades[i+1].price)    
                var arrow = '<i class="fa fa-angle-up" aria-hidden="true"></i>';
            else {
                var arrow = '<i class="fa fa-angle-down" aria-hidden="true"></i>';
            }

            if (trade.side == "sell"){
                trade_body_html += '<tr class="trades_tr" style="font-size:11px;line-height:16px;" data-price="' + trade.trade_id + '"><td>\n\
<div style="text-align:right;width:30%;float:left;top:0px;left:0px;"><span style="color:#d7d7d8;">' + parseFloat(trade.size).toFixed(8) + '</span></div>\n\
<div style="text-align:center;width:40%;float:left;color:#fd2d2f;"><span>' + parseFloat(trade.price).toFixed(2) + '&nbsp;</span>' + arrow + '</div>\n\
<div style="text-align:left;width:30%;float:left;"><span style="color:#d7d7d8;">' + trade.time + '</span></div></td></tr>';
            } else {
                trade_body_html += '<tr class="trades_tr" style="font-size:11px;line-height:16px;" data-price="' + trade.trade_id + '"><td>\n\
<div style="text-align:right;width:30%;float:left;top:0px;left:0px;"><span style="color:#d7d7d8;">' + parseFloat(trade.size).toFixed(8) + '</span></div>\n\
<div style="text-align:center;width:40%;float:left;color:#31ff31;"><span>' + parseFloat(trade.price).toFixed(2) + '&nbsp;</span>' + arrow + '</div>\n\
<div style="text-align:left;width:30%;float:left;"><span style="color:#d7d7d8;">' + trade.time + '</span></div></td></tr>';
            }
        }

        trade_tbl_str += trade_body_html + "</tbody></table>";
        
        $('#div_trades').html(trade_tbl_str);
        if (interval_num_trade > 0){
            stopLoading('trade');
        }
    });
}

function getOpenOrders() {
    if (interval_num_open_orders == 1)  {
        showLoading('open_orders');
    }
    $.get('getopenorders/' + front_asset + '-' + back_asset, function(resp){
        var orders = typeof resp == 'string' ? JSON.parse(resp) : resp;
        var tbl_str = '<table style="width:100%;float:left;"><tbody>';
        body_html = '';
        for (var i = 0; i < orders.length; i++) {
            var order = orders[i];
            var filled_str = '';
            if (order.order_side == "buy") 
                var color = "#31ff31";
            else
                var color = "#fd2d2f";
            var num = parseFloat(order.filled) / parseFloat(order.size);
            if (num > 1)    num = 1;
            
            filled_str = '<div style="float:left;width:40px;height:10px;border:1px solid ' + color + ';margin-top: 5px;"><div style="float:left;height:8px;width:' + num*38 + 'px;background-color:' + color + ';"></div></div>';

            body_html += '<tr class="open_orders_tr" style="font-size:11px;line-height:20px;" data-price="' + order.order_id + '"><td>\n\
<div style="text-align:center;width:10%;float:left;"><div style="color:#d7d7d8;border:1px solid #d7d7d8;border-radius:8px;height:16px;width: 16px;margin: auto;font-size:10px;"><div style="margin-top: -2px;">' + order.order_type.substr(0,1).toUpperCase() + '</div></div></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">' + parseFloat(order.size).toFixed(8) + '</span></div>\n\
<div style="text-align:center;width:15%;float:left;color:#d7d7d8;">' + filled_str + '<div>' + order.filled + '</div></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:' + color + ';">' + parseFloat(order.price).toFixed(2) + '</span></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">0</span></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">' + order.time + '</span></div>\n\
<div style="text-align:center;width:15%;float:left;"><span style="color:#d7d7d8;">' + order.status + '</span></div>\n\
</td></tr>';
        }
        tbl_str += body_html + "</tbody></table>";
        
        $('#div_open_orders').html(tbl_str);
        if (interval_num_open_orders > 0){
            stopLoading('open_orders');
        }
    });
}

function getFills() {
    if (interval_num_fills == 1)  {
        showLoading('fills');
    }
    $.get('getfilledlist/' + front_asset + '-' + back_asset, function(resp){
        var fills = typeof resp == 'string' ? JSON.parse(resp) : resp;
        var tbl_str = '<table style="width:100%;float:left;"><tbody>';
        body_html = '';
        for (i = 0; i < fills.length; i++) {
            var fill = fills[i];
            if (fill.order_side == "buy") 
                var color = "#fd2d2f";
            else
                var color =  "#31ff31";
            body_html += '<tr class="open_orders_tr" style="font-size:11px;line-height:18px;"><td>\n\
<div style="text-align:center;width:20%;float:left;"><span style="color:#d7d7d8;">' + parseFloat(fill.size).toFixed(8) + '</span></div>\n\
<div style="text-align:center;width:20%;float:left;"><span style="color:' + color + ';">' + parseFloat(fill.price).toFixed(2) + '</span></div>\n\
<div style="text-align:center;width:20%;float:left;"><span style="color:#d7d7d8;">0</span></div>\n\
<div style="text-align:center;width:20%;float:left;"><span style="color:#d7d7d8;">' + fill.time + '</span></div>\n\
<div style="text-align:center;width:20%;float:left;"><span style="color:#d7d7d8;">' + fill.product + '</span></div>\n\
</td></tr>';
        }
        tbl_str += body_html + "</tbody></table>";
        
        $('#div_fills').html(tbl_str);
        if (interval_num_fills > 0){
            stopLoading('fills');
        }
    });
}

function showLoading(str) {
    if (str == 'order_book')
        $('#order_book_loading').css('display', 'block');
    else if (str == 'trade')
        $('#trade_loading').css('display', 'block');
    else if (str == 'open_orders')
        $('#open_orders_loading').css('display', 'block');
    else if (str == 'fills')
        $('#fills_loading').css('display', 'block');
}

function stopLoading(str) {
    if (str == 'order_book')
        $('#order_book_loading').css('display', 'none');
    else if (str == 'trade')
        $('#trade_loading').css('display', 'none');
    else if (str == 'open_orders')
        $('#open_orders_loading').css('display', 'none');
    else if (str == 'fills')
        $('#fills_loading').css('display', 'none');
}

function zero_count(value){
    var str = value.toString();
    var tmp = str.split('.')[1];
    for (var i=7;i>=1;i--){
        if (tmp[i] != '0') {
            break;
        }
    }
    return 8 - i - 1;
}