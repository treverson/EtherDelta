/** Trade history **/
select ot.transaction_id trade_id, ot.a_amount size, ot.trade_price price, ob.order_side side, ob.order_date time
from order_transaction ot
join orderbook ob
on ob.order_id = ot.a_order_id
where ob.order_status = 'closed' and ob.offer_asset = 'BTC' and ob.want_asset = 'USD'
union
select ot.transaction_id trade_id, ot.a_amount size, ot.trade_price price, ob.order_side side, ob.order_date time
from order_transaction ot
join orderbook ob
on ob.order_id = ot.b_order_id
where ob.order_status = 'closed' and ob.offer_asset = 'BTC' and ob.want_asset = 'USD'
order by time;
select * from order_transaction;