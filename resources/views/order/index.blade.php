@extends('layouts.app')

@section('content')

    <script src="{{ asset('./assets/js/bootstrap.js') }}"></script>
    <script src="{{ asset('./assets/js/jquery-3.1.1.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('./assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('./assets/css/font-awesome.css') }}">

    <script src="{{ asset('./assets/js/amcharts.js') }}"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <script src="{{ asset('./assets/js/dataloader.js') }}"></script>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('./css/pickmeup.css') }}">
    <link rel="stylesheet" media="screen" href="{{ asset('./css/calendar.css') }}">
    <script src="{{ asset('./js/pickmeup.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('./css/style.css') }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('./css/cryptocoins.css') }}" type="text/css" media="all">
    <script src="{{ asset('./js/variable.js') }}"></script>
    <script src="{{ asset('./js/javascript.js') }}"></script>
    
    <link rel="stylesheet" href="{{ asset('./css/index.css') }}">
    <script src="{{ asset('./js/index.js') }}"></script>
    <script src="{{ asset('./js/ajax.js') }}"></script>
    <div class="banner">
        <div class="row section" style="padding: 20px 20px;">
            <div class="logo"><img src="{{ asset('images/centra logo.png') }}"/></div>
            <div class="erc_txt">DECENTRALIZED TRADING</div>
            <div class="erc_toggle">
                <input type="checkbox" data-toggle="toggle" data-size="mini" id="toggle_erc20" onchange='doOnERC20Toggle()'>
            </div>
        @if (!Auth::guest())
            <div id="banner_login" style="float:right;">
                    <div id="user">
                         <div id="username">{{ Auth::user()->name }}</div>
                         <div class="caret"></div>
                    </div>
                    <div id="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</div>          
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    </form>          
            </div>
        @endif
        </div>
    </div>
    <div class="content">
        <div class="sidebar">
            <div class="c-nav">
                <div id="netfee">NET FEE</div>
                <div class="asset_section">
                        <div class="asset_title">BITCOIN</div>
                        <div id="btc_usd" class="asset_item">
                            <div class="crypto_name">BTC/ETH</div>
                            <div class="crypto_price" class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>14.15818</div>
                            <div class="crypto_percent">3.66%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">BTC/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>4171.21294</div>
                            <div class="crypto_percent">5.14%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
                <div id="asset_ether" class="asset_section">
                        <div class="asset_title">ETHEREUM</div>
                        <div class="asset_item">
                            <div class="crypto_name">ETH/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.07014</div>
                            <div class="crypto_percent">0.77%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">ETH/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>299.80000</div>
                            <div class="crypto_percent" style="color:#fd2d2f;">2.79%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-down" aria-hidden="true"></i></div>
                        </div>
                </div>
                <div id="asset_litecoin" class="asset_section">
                        <div class="asset_title">LITECOIN</div>
                        <div class="asset_item">
                            <div class="crypto_name">LTC/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.01280</div>
                            <div class="crypto_percent">7.22%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">LTC/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.16956287</div>
                            <div class="crypto_percent">1.42%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">LTC/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>295.75</div>
                            <div class="crypto_percent">2.39%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
                <div id="asset_ctr" class="asset_section">
                        <div class="asset_title">CTR</div>
                        <div class="asset_item">
                            <div class="crypto_name">CTR/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.00219996</div>
                            <div class="crypto_percent">0.81%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">CTR/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.03667154</div>
                            <div class="crypto_percent">8.42%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">CTR/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>12.31333534</div>
                            <div class="crypto_percent">1.56%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true"></i></div>
                        </div>
                </div>
                <div class="asset_section">
                        <div class="asset_title">DASH</div>
                        <div class="asset_item">
                            <div class="crypto_name">DASH/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.07800118</div>
                            <div class="crypto_percent">7.22%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">DASH/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.94631755</div>
                            <div class="crypto_percent">0.36%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">DASH/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>333.3451</div>
                            <div class="crypto_percent">2.39%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
                <div class="asset_section">
                        <div class="asset_title">RIPPLE</div>
                        <div class="asset_item">
                            <div class="crypto_name">RIPPLE/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.00004692</div>
                            <div class="crypto_percent">7.22%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">RIPPLE/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.00082731</div>
                            <div class="crypto_percent">0.61%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">RIPPLE/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>0.19573300</div>
                            <div class="crypto_percent">2.39%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
                <div class="asset_section">
                        <div class="asset_title">MONERO</div>
                        <div class="asset_item">
                            <div class="crypto_name">MONERO/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.02272162</div>
                            <div class="crypto_percent">7.22%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">MONERO/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.28637943</div>
                            <div class="crypto_percent">6.58%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">MONERO/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>94.65298695</div>
                            <div class="crypto_percent">2.39%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
                <div class="asset_section">
                        <div class="asset_title">Zcash</div>
                        <div class="asset_item">
                            <div class="crypto_name">Zcash/BTC</div>
                            <div class="crypto_price"><i class="cc BTC" aria-hidden="true"></i>0.06883012</div>
                            <div class="crypto_percent">7.22%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">Zcash/ETH</div>
                            <div class="crypto_price"><i class="cc ETH" aria-hidden="true"></i>0.78352321</div>
                            <div class="crypto_percent">4.83%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                        <div class="asset_item">
                            <div class="crypto_name">Zcash/USDT</div>
                            <div class="crypto_price"><i class="cc USDT" aria-hidden="true"></i>290.65497112</div>
                            <div class="crypto_percent">2.39%</div>
                            <div class="crypto_rise"><i class="fa fa-chevron-up" aria-hidden="true" ></i></div>
                        </div>
                </div>
            </div>
            <div class="row orderform">
                    <div class="row margin">
                        <div class="navicon"><i class="fa fa-bars" aria-hidden="true"></i></div>
                        <div class="margin_txt">MARGIN TRADING</div>
                        <div class="margin_toggle">
                            <input type="checkbox" data-toggle="toggle" data-size="mini">
                        </div>
                    </div>
                <form class="form">
                    
                    <div class="logged_in">
                        <div class="txt_balance">Balance</div>
                        <div class="style1">
                            <div class="currency_name front_asset">BTC</div>
                            <div class="front_currency_price"></div>
                        </div>
                        <div class="style1">
                            <div class="currency_name back_asset">ETH</div>
                            <div class="back_currency_price"></div>
                        </div>
                        <div class="btn_style1">
                            <div id="deposit_btn">DEPOSIT</div>
                            <div id="withdraw_btn">WITHDRAW</div>
                        </div>
                    </div>
                    <ul class="trade_type">
                        <li class="market">MARKET</li>
                        <li class="limit">LIMIT</li>
                        <li class="stop">STOP</li>
                        <li class="stoplimit">STOP LIMIT</li>
                    </ul>
                    <div class="trade_side">
                        <div class="buy">BUY</div>
                        <div class="sell">SELL</div>
                    </div>
                    <div class="market_order" id="market_order">
                        <div class="section">
                            <div class="section_header">Amount</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" class="amount">
                                <span class='front_asset'></span>
                            </div>
                        </div>
                        <div class="order_total">
                            <div>
                                <span class='result'>You will pay.</span>
                                <span class='back_asset'></span>
                                <b>≈</b>
                            </div>
                            <div class="total">0.00000000</div>
                        </div>
                    </div>
                    <div class="limit_order">
                        <div class="section">
                            <div class="section_header">Amount</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="amount">
                                <span class='front_asset'></span>
                            </div>
                        </div>
                        <div class="section">
                            <div class="section_header">Limit Price</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="limit_price">
                                <span class='back_asset'></span>
                            </div>
                        </div>
                        <div class="advanced_section">
                            <div class="advanced_content">
                                <div class="section">
                                    <div class="header">Time in Force Policy</div>
                                    <select id="case">
                                        <option value="GTC">Good Til Cancelled</option>
                                        <option value="IOC">Immediate or Cancel</option>
                                        <option value="FOK">Fill or Kill</option>
                                        <option value="DAY">Day</option>
                                        <option value="GTDT">Good Till Date/Time</option>
                                    </select>
                                </div>
                                <div class="section cancel">
                                    <div class="header">Cancel After</div>
                                    <p><input type="text" id="calendar" /></P>
                                    <select id="cancel_time"></select>
                                </div>
                            </div>
                        </div>
                        <div class="order_total">
                            <div>
                                <b>Total</b>
                                <span class='front_asset'></span>
                                <b>≈</b>
                            </div>
                            <div class="total">
                                0.00
                            </div>
                        </div>   
                    </div>
                    <div class="stop_order">
                        <div class="section amount_section">
                            <div class="section_header">Amount</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="amount">
                                <span class='front_asset'></span>
                            </div>
                        </div>
                        <div class="section">
                            <div class="section_header">Stop Price</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="stop_price">
                                <span class='back_asset'></span>
                            </div>
                        </div>
                        <div class="order_total">
                            <div>
                                <b>Total</b>
                                <span class='front_asset'></span>
                                <b>≈</b>
                            </div>
                            <div class="total">
                                N/A
                            </div>
                        </div>
                    </div>
                    <div class="stoplimit_order">
                        <div class="section amount_section">
                            <div class="section_header">Amount</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="amount">
                                <span class='front_asset'></span>
                            </div>
                        </div>
                        <div class="section">
                            <div class="section_header">Stop Price</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="stop_price">
                                <span class='back_asset'></span>
                            </div>
                        </div>
                        <div class="advanced_section">
                            <div class="section_header">Limit Price</div>
                            <div class="order_form_input_box">
                                <input type="number" step="0.01" min="0" name="amount" placeholder="0.00" value="" autocomplete="off" class="limit_price">
                                <span class='back_asset'></span>
                            </div>
                        </div>
                        <div class="advanced_section">
                            <div class="advanced_content">
                                <div class="section">
                                    <div class="header">Time in Force Policy</div>
                                    <select id="case">
                                        <option value="GTC">Good Til Cancelled</option>
                                        <option value="IOC">Immediate or Cancel</option>
                                        <option value="FOK">Fill or Kill</option>
                                        <option value="DAY">Day</option>
                                        <option value="GTDT">Good Till Date/Time</option>
                                    </select>
                                </div>
                                <div class="section cancel">
                                    <div class="header">Cancel After</div>
                                    <p><input type="text" id="calendar" /></P>
                                    <select id="cancel_time"></select>
                                </div>
                            </div>
                        </div>
                        <div class="order_total">
                        <div>
                            <b>Total</b>
                            <span class='front_asset'></span>
                            <b>≈</b>
                        </div>
                        <div class="total">
                            N/A
                        </div>
                    </div>
                    </div>
                    
                    <button type="button" class="stateful_btn">Place buy order</button>
                    <div class="div_msg"> 
                        <span class="msg"></span>
                    </div>
                </form>
            </div>
        </div>
        <div class="main_content">
            <div class="row middle_panel">
                <div class="col-md-12">
                    <div class="row price_chart_panel panel">
                        <div class="price_chart_header">
                            <div class="title">PRICE CHART</div>
                            <div class="trade_info_bar">
                                <div class="market_stat">
                                    <span class="num">0.00000000</span><span class="back_asset"></span>
                                    <span class="descr">Last trade price</span>
                                </div>
                                <div class="price_up">
                                    <span class="sign">+</span>
                                    <span class="num">9.46 %</span>
                                    <span class="descr">24 hour price</span>
                                </div>
                                <div class="day_volume">
                                    <span class="num">55991&nbsp;</span><span class="front_asset"></span>
                                    <span class="descr">24 hour volume</span>
                                </div>
                            </div>
                        </div>
                        <div class="price_chart_content">
                            <div id = 'option_container'  >
                                <select id = 'sel_chart' class = 'select_box_style' >					
                                    <option value = 'candlestick'>Candle</option>
                                    <option value = 'line'>Line</option>
                                </select>
                                <select id = 'sel_type' class = 'select_box_style' >
                                    <option value = 60>1m</option>
                                    <option value = 300>5m</option>
                                    <option value = 900>15m</option>
                                    <option value = 3600>1h</option>
                                    <option value = 21600>6h</option>
                                    <option value = 86400>1d</option>
                                </select>

                                <div class = 'sel_bigchart' >
                                    <font id = 'price_c' onclick = "change_style('price');" style = 'cursor: pointer;' >Price Chart</font>&nbsp;&nbsp;
                                    <font id = 'depth_c' onclick = "change_style('depth');" style = 'cursor: pointer;'>Depth Chart</font>
                                </div>

                            </div>

                            <div id = 'chartContain' >
                                <div id="chartdiv" class = 'chart_div'  >
                                    <div id="curtain" class="loader">Loading...</div>
                                </div>
                            </div>
                            <div id = 'chartContain_2' >
                                <div id="chartdiv_2" class = 'chart_div'  >
                                    <div id="curtain_2" class="loader">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row order_book_panel panel">
                        <div class="order_book_header">
                            <div class="title">ORDER BOOK</div>
                            <div class="order_trade_tabs">
                                <div class="order_tab">Order book</div>
                                <div class="trade_tab">Trade history</div>
                            </div>
                        </div>
                        <div class="order_book_content">
                            <div class="table_head">
                                <div class="sell_my_size">My size</div>
                                <div class="sell_market_size">Market size</div>
                                <div class="sell_price">Price (<span class='back_asset'></span>)</div>
                                <div class="blank"></div>
                                <div class="buy_price">Price (<span class='back_asset'></span>)</div>
                                <div class="buy_market_size">Market size</div>  
                                <div class="buy_my_size">My size</div>
                            </div>
                            <div class="table_content">
                                <div class="wrapper">
                                    <div id="order_book_loading" class="loader">Loading...</div>
                                    <div id="div_asks_bids"></div>
                                </div>
                            </div>
                            <div class="aggregation">
                                <div class="column">
                                    <div class="text">Aggregation</div>
                                    <div class="value">1.00</div>
                                </div>
                                <div class="buttons">
                                    <div class="aggregation_dec"></div>
                                    <div class="aggregation_inc"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row trade_history_panel panel">
                        <div class="trade_history_header">
                            <div class="title">TRADE HISTORY</div>
                            <div class="order_trade_tabs">
                                <div class="order_tab">Order book</div>
                                <div class="trade_tab">Trade history</div>
                            </div>
                        </div>
                        <div class="trade_history">
                            <div class="table_header">
                                <div class="trade_size">Trade size</div>
                                <div class="price">Price (<span class='back_asset'></span>)</div>
                                <div class="time">Time</div>
                            </div>
                            <div class="table_content">
                                    <div class="wrapper">
                                    <div id="trade_loading" class="loader">Loading...</div>
                                    <div id="div_trades"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row bottom_panel">
                <div class="open_orders_panel panel">
                    <div class="open_orders_header">
                        <div class="title">OPEN ORDERS</div>
                        <div class="open_fill_tabs">
                            <div class="open_tab">Open orders</div>
                            <div class="fill_tab">Fills</div>
                        </div>
                    </div>
                    <div class="open_orders_content">
                        <div class="table_head">
                            <div class="order_type">Type</div>
                            <div class="size">Size</div>
                            <div class="filled">Filled (<span class='front_asset'></span>)</div>
                            <div class="price">Price (<span class='back_asset'></span>)</div>
                            <div class="fee">Fee (<span class='back_asset'></span>)</div>
                            <div class="time">Time</div>
                            <div class="status">Status</div>
                        </div>
                        <div class="table_content">
                            <div class="wrapper">
                                <div id="open_orders_loading" class="loader">Loading...</div>
                                <div id="div_open_orders"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fills_panel panel">
                    <div class="fills_header">
                        <div class="title">FILLS</div>
                        <div class="open_fill_tabs">
                            <div class="open_tab">Open orders</div>
                            <div class="fill_tab">Fills</div>
                        </div>
                    </div>
                    <div class="fills_content">
                        <div class="table_head">
                            <div class="size">Size(<span class='front_asset'></span>)</div>
                            <div class="price">Price (<span class='back_asset'></span>)</div>
                            <div class="fee">Fee (<span class='back_asset'></span>)</div>
                            <div class="time">Time</div>
                            <div class="status">Product</div>
                        </div>
                        <div class="table_content">
                            <div class="wrapper">
                                <div id="fills_loading" class="loader">Loading...</div>
                                <div id="div_fills"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        <div class="footer">
                <div class="trade_btn">TRADE</div>
                <div class="book_btn">BOOK</div>
                <div class="charts_btn">CHARTS</div>
                <div class="orders_btn">ORDERS</div>
            </div>
    </div>
    <div class="etherdelta">
        <iframe src="http://localhost:6001"></iframe>
    </div>
    <div class='deposit_bg'>
        <div class='deposit_dlg'>
            <form role="form">
                <div class="form-group">
                    <label class="front_asset font_style_1"></label>
                    <input type="number" class="form-control" id="front_asset_value" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="back_asset font_style_1"></label>
                    <input type="number" class="form-control" id="back_asset_value" placeholder="0.00">
                </div>
                <div class='div_buttons'>
                    <button type="button" class="btn btn-success" id='btn_deposit_ok'>Ok</button>
                    <button type="button" class="btn btn-success" id='btn_deposit_cancel'>Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('./js/app.js') }}"></script>
@endsection
