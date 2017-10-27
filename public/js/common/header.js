var real_conn = new WebSocket('wss://ws-feed.gdax.com');
var sel_point = 'a_btc_usd';
var market_flag = 'buy';
var amount,
  unit_price,
  exchange,
  market_type,
  order_type,
  total_amount;
var time_in_force;
$(document).ready(function() {
  $('input[name="radio_market"]').click(function() {
    market_flag = $(this).attr('id');
    $('#btn_market').html("PLACE " + market_flag.toUpperCase() + " ORDER");
  });
  var product_id = sel_point.replace("a_", "").split("_").join("-").toUpperCase();


  setMarks();
  //alert(product_id)
  // real_conn.onopen = function() {
  //   var request_param = {
  //     "type": "subscribe",
  //     "product_id": product_id /*["BTC-USD", "BTC-EUR", "BTC-GBP", "ETH-USD", "ETH-BTC", "ETH-EUR", "LTC-USD", "LTC-BTC", "LTC-EUR"] */
  //   };
  //   real_conn.send(JSON.stringify(request_param));
  // }
  // real_conn.onmessage = function(onmsg) {
  //   var data = JSON.parse(onmsg.data);
  //   if (data.side == "buy" && data.type == "open") {
  //     // console.log(data);
  //   }
  // //console.log(JSON.parse(onmsg.data));
  // }
  //alert(123);
  var mHeight = parseInt(window.innerHeight) - 70;
  $('.container-body').css('height', mHeight + 'px');
  $('#main_body').height(mHeight + 'px');
  $("input[name='radio_market']").click(function() {
    order_type = $(this).attr('id');
    product_id = sel_point.replace("a_", "").toUpperCase().split("_");
    if (order_type == 'buy') {
      $('#span_amount').html(product_id[0]);
      $('#span_total_amount').html(product_id[1]);
    } else {
      $('#span_amount').html(product_id[1]);
      $('#span_total_amount').html(product_id[0]);
    }
  });
  $("input[name='radio_limit']").click(function() {
    order_type = $(this).attr('id');
    product_id = sel_point.replace("a_", "").toUpperCase().split("_");
    $('#btn_limit').html("PLACE " + order_type.toUpperCase() + " ORDER");
  });

  $("input[name='radio_stop']").click(function() {
    order_type = $(this).attr('id');
    product_id = sel_point.replace("a_", "").toUpperCase().split("_");
    if (order_type == 'sell') {
      $('#span_stop_amount').html(product_id[0]);
      $('#span_stop_total_amount').html(product_id[1]);
    } else {
      $('#span_stop_amount').html(product_id[1]);
      $('#span_stop_total_amount').html(product_id[0]);
    }
    $('#btn_stop').html("PLACE " + order_type.toUpperCase() + " ORDER");
  });

  $('#a_point').html('BTC/USD');

  getRealTimeData();
  getOrderData();
  getTradeData();

  $('.a-sub-menu').click(function() {
    sel_point = $(this).attr('id');
    var title = sel_point.replace("a_", "").split("_").join("/").toUpperCase();
    $('#a_point').html(title);
    getRealTimeData();
    getOrderData();
    getTradeData();
    setMarks();
  });
  $('#amount').keyup(function(event) {
    if (order_type == 'buy') {
      total_amount = amount * unit_price;
    } else {
      total_amount = amount / unit_price;
    }
    $('#first_currency_value').html(total_amount);
  });
  /*******  Market part start *********/
  $('#btn_market').click(function() {
    if (order_type == 'buy') {
      total_amount = amount * unit_price;
    } else {
      total_amount = amount / unit_price;
    }
    amount = $('#amount').val();
    unit_price = parseFloat($('#a_price').attr('cur_price'));
    exchange = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
    market_type = 'market';
    order_type = $("input[name='radio_market']:checked").attr('id');
    $.post('setorder', {
      amount: amount,
      unit_price: unit_price,
      total_amount: total_amount,
      exchange: exchange,
      market_type: market_type,
      order_type: order_type
    }, function(resp) {
      alert(resp);
    });
  });
  /*********  Market part end ***********/
  /****  limit part start ***************/
  $('#post_only').click(function() {
    $(this).val((1 - parseInt($(this).val())));
  });
  $('#btn_limit').click(function() {
    unit_price = parseFloat($('#a_price').attr('cur_price'));
    exchange = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
    market_type = 'limit';
    order_type = $("input[name='radio_limit']:checked").attr('id');
    limit_amount = $('#limit_amount').val();
    limit_price = $('#limit_price').val();
    limit_total_amount = parseFloat(limit_amount) * parseFloat(limit_price);
    time_in_force = $("input[name='time_in_force']:checked").val();
    post_only = $("#post_only").val();
    $.post('setlimit', {
      unit_price: unit_price,
      exchange: exchange,
      market_type: market_type,
      order_type: order_type,
      limit_amount: limit_amount,
      limit_price: limit_price,
      limit_total_amount: limit_total_amount,
      time_in_force: time_in_force,
      post_only: post_only
    }, function(resp) {
      alert(resp);
    });
  });
  /*********** limit part end *******/
  /*********** stop part start *******/
  $('#btn_stop').click(function() {
    unit_price = parseFloat($('#a_price').attr('cur_price'));
    exchange = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
    market_type = 'stop';
    order_type = $("input[name='radio_stop']:checked").attr('id');
    stop_amount = $('#stop_amount').val();
    stop_price = $('#stop_price').val();
    stop_limit_price = $('#stop_limit_price').val();
    if (order_type == 'buy')
      stop_total_amount = parseFloat(stop_amount) / parseFloat(stop_limit_price) || 0;
    else
      stop_total_amount = parseFloat(stop_amount) * parseFloat(stop_limit_price) || 0;

    $.post('setstop', {
      unit_price: unit_price,
      exchange: exchange,
      market_type: market_type,
      order_type: order_type,
      stop_amount: stop_amount,
      stop_price: stop_price,
      stop_limit_price: stop_limit_price,
      stop_total_amount: stop_total_amount
    }, function(resp) {
      alert(resp);
    });
  });
/*********** stop part end *******/
});

window.onresize = function(event) {
  var mHeight = parseInt(window.innerHeight) - 70;
  // console.log(window, document, screen, parseInt(screen.availHeight), 50);
  // //alert(mHeight);
  $('.container-body').css('height', mHeight + 'px');
  $('#main_body').height(mHeight + 'px');
};
window.setInterval(function() {
  getRealTimeData();
  getOrderData();
  getTradeData();
}, 1000);
function getRealTimeData() {
  var product_id = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
  var url = 'https://api.gdax.com/products/' + product_id + '/ticker'
  $.get(url, function(resp) {
    var tmp = product_id.split('-');
    $('#a_price').html(makeNumber(resp.price) + tmp[1]);
    $('#a_price').attr('cur_price', resp.price);
    $('#a_volume_amount').html(makeNumber(resp.volume) + tmp[1]);

    amount = $('#amount').val();
    unit_price = parseFloat($('#a_price').attr('cur_price'));
    exchange = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
    market_type = 'market';
    order_type = $("input[name='radio_market']:checked").attr('id');
  //total_amount = amount * unit_price;
  })
}
function getOrderData() {
  var product_id = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
  var url = 'getorderbooklist/' + product_id
  $.get(url, function(resp) {
    // resp = JSON.parse(resp);
    var asks = resp.sell;
    var bids = resp.buy;
    var tmp = product_id.split('-');
    var bids_tbl_str = '<table style="width:100%;float:left;"><tbody>';
    var asks_tbl_str = '<table style="width:100%;float:left;"><tbody>';
    bids_body_html = '';
    asks_body_html = '';
    // console.log(asks);return;
    for (i = 0; i < bids.length; i++) {
      bidObj = bids[i];
      bids_rate = 100;
      bids_body_html += '<tr class="asks_tr" style="line-height:16px;" data-price="' + bidObj.amount + '"><td><div style="height:16px;width: 100%;float:right;background:rgba(0,0,188,0.8);background-size:' + bids_rate + '% 15px;background-repeat:no-repeat;background-position: right 0px;"><div style="text-align:right;width:33%;float:left;top:0px;left:0px;"><span style="color:white;">' + bidObj.amount + '</span><span style="color:#686868;">00000000</span>&nbsp;</div><div style="text-align:right;width:33%;float:left;"><span style="color:white;">' + bidObj.trade_price + '</span><span style="color:#686868;">00000</span>&nbsp;</div><div style="text-align:right;width:33%;float:left;"><span style="color:white;">' + parseFloat(parseFloat(bidObj.amount) * parseFloat(bidObj.trade_price)).toFixed(2) + '</span><span style="color:#686868;">000000</span>&nbsp;</div></td></tr>';
    }
    for (i = 0; i < asks.length; i++) {
      asks_rate = 100;
      askObj = asks[i];
      asks_body_html += '<tr class="asks_tr" style="line-height:16px;" data-price="' + askObj.amount + '"><td><div style="height:16px;width: 100%;float:right;background:rgba(108,0,0,0.8);background-size:' + asks_rate + '% 15px;background-repeat:no-repeat;background-position: right 0px;"><div style="text-align:right;width:33%;float:left;top:0px;left:0px;"><span style="color:white;">' + askObj.amount + '</span><span style="color:#686868;">00000000</span>&nbsp;</div><div style="text-align:right;width:33%;float:left;"><span style="color:white;">' + askObj.trade_price + '</span><span style="color:#686868;">00000</span>&nbsp;</div><div style="text-align:right;width:33%;float:left;"><span style="color:white;">' + parseFloat(parseFloat(askObj.amount) * parseFloat(askObj.trade_price)).toFixed(2) + '</span><span style="color:#686868;">000000</span>&nbsp;</div></td></tr>';
    }

    bids_tbl_str += bids_body_html + "</tbody></table>";
    asks_tbl_str += asks_body_html + "</tbody></table>";

    $('#span_bid_price').html(bids[0].amount);
    $('#span_bid_size').html(parseFloat(bids[0].trade_price).toFixed(2));
    $('#span_bid_rate').html(parseFloat(parseFloat(bids[0].amount) * parseFloat(bids[0].trade_price)).toFixed(2));
    $('#div_asks').html(asks_tbl_str);
    $('#div_bids').html(bids_tbl_str);
  })
}
function getTradeData() {
  var product_id = sel_point.replace("a_", "").split("_").join("-").toUpperCase();
  var url = 'https://api.gdax.com/products/' + product_id + '/trades'
  $.get(url, function(resp) {
    var trades = resp;
    var tmp = product_id.split('-');
    var trade_tbl_str = '<table style="width:100%;float:left;"><tbody>';
    trade_body_html = '';
    for (i = 0; i < trades.length; i++) {
      var trade = trades[i];
      var tmp = trade.time.split('.')[0].split('T')[0].split('-');
      var time_str = tmp[1] + '-' + tmp[2] + ' ' + trade.time.split('.')[0].split('T')[1];
      if (i == trades.length - 1) {
        nextTrade = trades[i];
      } else {
        nextTrade = trades[i + 1];
      }
      if (trade.price > nextTrade.price) {
        arrow_class = "fa fa-chevron-up i-green";
        i_color = "i-green";
      } else if (trade.price < nextTrade.price) {
        arrow_class = "fa fa-chevron-down i-red";
        i_color = "i-red";
      } else {
        arrow_class = 'i-green';
        i_color = "i-green";
      }

      trade_body_html += '<tr class="asks_tr" style="line-height:16px;" data-price="' + trade.trade_id + '"><td><div style="height:16px;width: 100%;float:right;"><div style="text-align:right;width:33%;float:left;top:0px;left:0px;"><i class="' + arrow_class + '"></i>&nbsp;&nbsp;<span style="color:white;">' + parseFloat(trade.price).toFixed(2) + '</span><span style="color:#686868;">00000</span>&nbsp;</div><div style="text-align:center;width:33%;float:left;"><span style="color:white;">' + trade.size + '</span><span style="color:#686868;"></span>&nbsp;&nbsp;<i class="fa fa-circle ' + i_color + '" style="font-size:6px;"></i></div><div style="text-align:right;width:33%;float:left;"><span style="color:white;">' + time_str + '</span>&nbsp;&nbsp;&nbsp;</div></td></tr>';
    }

    trade_tbl_str += trade_body_html + "</tbody></table>";
    $('#div_trades').html(trade_tbl_str);
  });
}
function setMarks() {
  var tmp_val = sel_point.replace("a_", "").split("_");
  $('#currency1').html(tmp_val[0].toUpperCase());
  $('#currency2').html(tmp_val[1].toUpperCase());
}
function makeNumber(n) {
  return parseFloat(n).toFixed(2).replace(/./g, function(c, i, a) {
    return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
  });
}
