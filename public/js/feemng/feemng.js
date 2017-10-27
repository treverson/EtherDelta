var want_asset = 'BTC';
var offer_asset = 'USDT';
$(document).ready(function(){
    loadAssetFeeData(want_asset+'-'+offer_asset);
    $('#want_asset').change(function() {
        want_asset = $(this).val();
        loadAssetFeeData(want_asset+'-'+offer_asset);
    });
    $('#offer_asset').change(function() {
        offer_asset = $(this).val();
        loadAssetFeeData(want_asset+'-'+offer_asset);
    });
    $('#offer_asset').val(offer_asset);
    $('#btn_save').click(function(){
        want_asset = $('#want_asset').val();
        offer_asset = $('#offer_asset').val();
        var bottom = $('#bottom').val();
        var top = $('#top').val();
        var fee = $('#fee').val();
        var rebate = $('#rebate').val();
        var post_param = {want_asset: want_asset, offer_asset:offer_asset, bottom:bottom, top:top, fee: fee, rebate: rebate};     
        $.post('feesave', post_param, function(resp) {
            
            if ( resp === 'SUCCESS' ) {
                loadAssetFeeData(want_asset+'-'+offer_asset);
            }

console.log(resp);
        });
    });
});
function loadAssetFeeData( product ) {
    $.get('getassetfeedata/'+product, function( asset_json_data ) {
        if ( typeof asset_json_data == 'string' ) asset_json_data = JSON.parse(asset_json_data);
        asset_fee_data_html = '';
        // for(i in asset_json_data) {
        for(i=0;i<asset_json_data.length;i++) {
            fee_data = asset_json_data[i];
            asset_fee_data_html += '<tr><td>'+fee_data.bottom+"&lt;"+fee_data.want_asset+"&lt;="+fee_data.top+'</td>';
            asset_fee_data_html += '<td>'+fee_data.fee+'%</td>';
            asset_fee_data_html += '<td>'+fee_data.rebate+'%</td>';
            asset_fee_data_html += '<td>'+fee_data.updated_at+'</td>';
            asset_fee_data_html += '<td>'+fee_data.created_at+'</td></tr>';
        }
        $('#asset_fee_data').html(asset_fee_data_html);
        console.log(asset_json_data);
    });
}