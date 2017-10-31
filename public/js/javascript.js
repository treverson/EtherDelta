var chart = AmCharts.makeChart("chartdiv", {
  "type": "serial",
  "theme": "light",
  "categoryField": 'date',
  "valueAxes": [{
    'id': 'a1',
    "position": "right",
    'axisColor': 'white',
    'color': 'white',
    'unit': '',
    'unitPosition': 'left',
  }, {
    "id": "a2",
    "gridAlpha": 0,
    'axisColor': 'white',
    'color': 'white',
    "axisAlpha": 1,
    "minimum": 0,
    "minMaxMultiplier": 2,
  }],
  "allLabels": [{
    "text": "(ETH)",
    "rotation": 0,
    "x": "!55",
    "y": "0",
    "width": "50%",
    "size": 10,
    "bold": false,
    "align": "right",
    "color": 'white'
  }],
  "graphs": [{
    "id": "g1",
    "proCandlesticks": true,
    "balloonText": "<table style = 'font-size: 12px;' ><tr><td>Open</td><td>[[open]]</td></tr><tr><td>Low</td><td>[[low]]</td></tr><tr><td>High</td><td>[[high]]</td></tr><tr><td>Close</td><td>[[close]]</td></tr><tr><td>Volume</td><td>[[volume]]</td></tr></table>        <!--    Open:<b>[[open]]</b><br>Low:<b>[[low]]</b><br>High:<b>[[high]]</b><br>Close:<b>[[close]]</b><br>Volume:<b>[[volume]]</b -->",
    "closeField": "close",
    "fillColors": "#82f464",
    "fillAlphas": 0.9,
    "highField": "high",
    "lineColor": '#82f464',
    "lineAlpha": 1,
    "lowField": "low",
    "negativeFillColors": "#fe59ba",
    "negativeLineColor": "#fe59ba",
    "openField": "open",
    "title": "close:",
    "type": 'candlestick',
    "valueAxis": "a1",
    "valueField": "close",
  },

  {
    "id": "g2",
    'title': 'volume',
    'balloonText': '',
    "colorField": "color_field",
    "lineColor": 'transparent',
    "fillAlphas": 0.9,
    "type": "column",
    "valueAxis": "a2",
    "valueField": "volume",
  }],
  "chartCursor": {
    "valueLineEnabled": true,
    "valueLineBalloonEnabled": true,
    "cursorColor": 'white',
    "color": '#000000',
    "cursorAlpha": 0.5,
    "zoomable": false
  },
  "categoryField": "date",
  "categoryAxis": {
    'axisColor': 'white',
    'color': 'white',
    "minPeriod": "mm",
    "parseDates": true,

  },
  "dataProvider": [],
});

function requestData(sel_type, graphType, axis_label, market_type, point) {
  var fillcolor;
  var dataProvider = [];
  point.forEach(function (element, index) {
    year = new Date(point[index]['time']).getFullYear();
    month = new Date(point[index]['time']).getMonth() + 1;
    day = new Date(point[index]['time']).getDate();
    hour = new Date(point[index]['time']).getHours();
    minutes = new Date(point[index]['time']).getMinutes();
    time = day + " " + hour + ":" + minutes;
    if (point[index]['side'] == 'buy') {
      fillcolor = 'red';
    }
    else {
      fillcolor = 'blue';
    }
    var itemPoint = {
      "date": point[index]['time'], //'' + time , 
      "open": point[index]['open'].toString(),
      "high": point[index]['high'].toString(),
      "low": point[index]['low'].toString(),
      "close": point[index]['close'].toString(),
      "volume": point[index]['volume'],
      'color_field': fillcolor,
    }
    if (dataProvider.length < 60) {
      dataProvider.push(itemPoint);
    }

  }, this);

  switch (sel_type) {
    case 60:
      chart.categoryAxis.minPeriod = 'ss';
      break;
    case 300:
      chart.categoryAxis.minPeriod = 'ss';
      break;
    case 900:
      chart.categoryAxis.minPeriod = 'ss';
      break;
    case 3600:
      chart.categoryAxis.minPeriod = 'mm';
      break;
    case 21600:
      chart.categoryAxis.minPeriod = 'mm';
      break;
    case 86400:
      chart.categoryAxis.minPeriod = 'mm';
      break;
  }
  if (typeof graphType === 'undefined') {
    graphType = 'candlestick';
  }

  if (graphType == 'line') {
    chart.graphs[0].fillAlphas = 0.2;
    chart.graphs[0].fillColors = ["#3d84d6", "#000000"];
    chart.graphs[0].lineColor = "#3d84d6";
  }
  else {
    chart.graphs[0].fillAlphas = 0.9;
    chart.graphs[0].fillColors = "#46ffd6";
    chart.graphs[0].lineColor = '#46ffd6';
  }
  chart.allLabels[0].text = axis_label;

  dataProvider.reverse();

  chart.graphs[0].type = graphType;
  chart.dataProvider = dataProvider;
  chart.validateData();
  $('#curtain').css('display', 'none');
}

function processData(list, type, desc) {

  var res = [];
  // Convert to data points
  for (var i = 0; i < list.length; i++) {
    list[i] = {
      value: Number(list[i][0]),
      volume: Number(list[i][1]),
    }
  }

  // Sort list just in case
  list.sort(function (a, b) {
    if (a.value > b.value) {
      return 1;
    }
    else if (a.value < b.value) {
      return -1;
    }
    else {
      return 0;
    }
  });

  // Calculate cummulative volume
  if (desc) {
    for (var i = list.length - 1; i >= 0; i--) {
      if (i < (list.length - 1)) {
        list[i].totalvolume = list[i + 1].totalvolume + list[i].volume;
      }
      else {
        list[i].totalvolume = list[i].volume;
      }
      var dp = {};
      dp["value"] = list[i].value;
      dp[type + "volume"] = list[i].volume;
      dp[type + "totalvolume"] = list[i].totalvolume;
      res.unshift(dp);
    }
  }
  else {
    for (var i = 0; i < list.length; i++) {
      if (i > 0) {
        list[i].totalvolume = list[i - 1].totalvolume + list[i].volume;
      }
      else {
        list[i].totalvolume = list[i].volume;
      }
      var dp = {};
      dp["value"] = list[i].value;
      dp[type + "volume"] = list[i].volume;
      dp[type + "totalvolume"] = list[i].totalvolume;
      res.push(dp);
    }
  }

  return res;
}

function showDepthChart(axis_label, market_type, datas) {
  
  var bids_list = new Array(60);
  for (var i = 0; i < 60; i++) {
    bids_list[i] = new Array(3);
    bids_list[i][0] = (typeof datas.bids[i] != 'undefined') ? datas.bids[i].price : 0;
    bids_list[i][1] = (typeof datas.bids[i] != 'undefined') ? datas.bids[i].size : 0;
    bids_list[i][2] = (typeof datas.bids[i] != 'undefined') ? datas.bids[i].num_orders : 0;
  }
  var asks_list = new Array(60);
  for (var i = 0; i < 60; i++) {
    asks_list[i] = new Array(3);
    asks_list[i][0] = (typeof datas.asks[i] != 'undefined') ? datas.asks[i].price : 0;
    asks_list[i][1] = (typeof datas.asks[i] != 'undefined') ? datas.asks[i].size : 0;
    asks_list[i][2] = (typeof datas.asks[i] != 'undefined') ? datas.asks[i].num_orders : 0;
  }
  var data = {
    bids: bids_list,
    asks: asks_list
  };

  bids_data = processData(data.bids, "bids", true);
  asks_data = processData(data.asks, "asks", false);

  bids_data.push.apply(bids_data, asks_data);
console.log('bids_data', bids_data);
  res = bids_data;
  chart_2.dataProvider = res;
  chart_2.allLabels[0].text = axis_label;
  chart_2.validateData();
  $('#curtain_2').css('display', 'none');
}

var chart_2 = AmCharts.makeChart("chartdiv_2", {
  "type": "serial",
  "theme": "light",
  "chartCursor": {
    "cursorColor": '#ffffff',
    "color": '#000000',
    "cursorAlpha": 0.5,
    "zoomable": false
  },
  "allLabels": [{
    "text": "(ETH)",
    "rotation": 0,
    "x": "85",
    "y": "!24",
    "width": "50%",
    "size": 10,
    "bold": false,
    "align": "right",
    "color": 'white'
  }],
  "graphs": [{
    "id": "bids",
    "fillAlphas": 0.1,
    "lineAlpha": 1,
    "lineThickness": 2,
    "lineColor": "#31ff31",
    "type": "step",
    "valueField": "bidstotalvolume",
    "balloonFunction": balloon
  }, {
    "id": "asks",
    "fillAlphas": 0.1,
    "lineAlpha": 1,
    "lineThickness": 2,
    "lineColor": "#fd2d2f",
    "type": "step",
    "valueField": "askstotalvolume",
    "balloonFunction": balloon
  }, /*{
    "lineAlpha": 0,
    "fillAlphas": 0.2,
    "lineColor": "#000",
    "type": "column",
    "clustered": false,
    "valueField": "bidsvolume",
    "showBalloon": false
  }, {
    "lineAlpha": 0,
    "fillAlphas": 0.2,
    "lineColor": "#000",
    "type": "column",
    "clustered": false,
    "valueField": "asksvolume",
    "showBalloon": false
  }*/],
  "categoryField": "value",
  "balloon": {
    "textAlign": "left"
  },
  "valueAxes": [{
    'position': 'left',
    'axisColor': 'white',
    'color': 'white'
  }],
  "categoryAxis": {
    "minHorizontalGap": 100,
    "startOnAxis": true,
    "showFirstLabel": false,
    "showLastLabel": false,
    'axisColor': 'white',
    'color': 'white',
    'labelFunction': formatLabel
  },
});

function balloon(item, graph) {
  var txt;
  if (graph.id == "asks") {
    txt = "Ask: <strong>" + formatNumber(item.dataContext.value, graph.chart, 4) + "</strong><br />"
      + "Total volume: <strong>" + formatNumber(item.dataContext.askstotalvolume, graph.chart, 4) + "</strong><br />"
      + "Volume: <strong>" + formatNumber(item.dataContext.asksvolume, graph.chart, 4) + "</strong>";
  }
  else {
    txt = "Bid: <strong>" + formatNumber(item.dataContext.value, graph.chart, 4) + "</strong><br />"
      + "Total volume: <strong>" + formatNumber(item.dataContext.bidstotalvolume, graph.chart, 4) + "</strong><br />"
      + "Volume: <strong>" + formatNumber(item.dataContext.bidsvolume, graph.chart, 4) + "</strong>";
  }
  return txt;
}

function formatNumber(val, chart, precision) {
  return AmCharts.formatNumber(
    val,
    {
      precision: precision ? precision : chart.precision,
      decimalSeparator: chart.decimalSeparator,
      thousandsSeparator: chart.thousandsSeparator
    }
  );
}

function formatLabel(value, valueString, axis) {
  // let's say we dont' want minus sign next to negative numbers
  if (value > 0) {
    valueString = value;
  }
  else {
    valueString = '';
  }
  return valueString;
}

function change_style(sel) {
  if (sel == 'price') {
    chart_selection = 'price';
    $('#chartContain').css('display', 'block');
    $('#chartContain_2').css('display', 'none');

    $("#price_c").css('color', '#fff');
    $("#price_c").css('border-bottom', '1px solid #fff');
    $("#depth_c").css('color', 'hsla(206,8%,82%,.6)');
    $("#depth_c").css('border-bottom', 'hsla(206,8%,82%,.6)');
  }
  else {
    chart_selection = 'depth';

    $('#chartContain').css('display', 'none');
    $('#chartContain_2').css('display', 'block');

    $("#price_c").css('color', 'hsla(206,8%,82%,.6)');
    $("#price_c").css('border-bottom', 'hsla(206,8%,82%,.6)');
    $("#depth_c").css('color', '#fff');
    $("#depth_c").css('border-bottom', '1px solid #fff');
  }
}