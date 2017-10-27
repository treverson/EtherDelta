var order_side = 'buy';
var order_type = 'limit';
var offer_asset = 'BTC';
var want_asset = 'USD';
var customer_id = 1;
$(document).ready(function() {
  $("input[name='order_side']").click(function() {
    order_side = $(this).val();
    $('#btn_action').html($(this).val())
  });
  $("input[name='order_type']").click(function() {
    order_type = $(this).val();
  });
  $("input[name='customer_id']").click(function() {
    customer_id = $(this).val();
  });
  $('#btn_action').click(function() {
    var post_param = {};
    var quantity = $('#amount').val();
    var price = $('#limit_price').val();
    var stop_price = $('#stop_price').val();
    post_param = {
      customer_id: customer_id,
      order_side: order_side,
      order_type: order_type,
      quantity: quantity,
      price: price,
      stop_price: stop_price,
      offer_asset: offer_asset,
      want_asset: want_asset
    };
    $.post('addorder', post_param, function(resp) {
      console.log(resp);
    });
  });
  $('#btn_open_order').click(function() {
    $.get('getopenorders/BTC-USD', function(open_orders) {

      rowHTML = '';
      for (i = 0; i < open_orders.length; i++) {
        var row = open_orders[i];
        rowHTML += "<tr>" + "<td></td><td>" + row.quantity + "</td><td>" + row.filled_amount + "</td><td>" + row.price + "</td><td>0</td><td>" + row.order_date + "</td><td>" + row.order_status + "</td></tr>";
      }
      $('#tbody_open_orders').html(rowHTML);
    });
  });
  $('#btn_order_fill').click(function() {
    $.get('getfilledlist/BTC-USD', function(filled_list) {
console.log('asd');
      console.log(filled_list);
    });
  });
});
