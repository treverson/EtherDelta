var chart = AmCharts.makeChart("chartdiv", {
  "type": "serial",
  "theme": "light",
  "categoryField": 'date',
  "valueAxes": [{
    "position": "right",
    'axisColor': 'white',
    'color': 'white',
    'unit': '',
    'unitPosition': 'left',
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
    "balloonText": "Open:<b>[[open]]</b><br>Low:<b>[[low]]</b><br>High:<b>[[high]]</b><br>Close:<b>[[close]]</b><br>",
    "closeField": "close",
    "fillColors": "#82f464",
    "fillAlphas": 0.9,
    "highField": "high",
    "lineColor": '#82f464',
    "lineAlpha": 1,
    "lowField": "low",
    "negativeFillColors": "#fd2d2f",
    "negativeLineColor": "#fd2d2f",
    "openField": "open",
    "title": "Price:",
    "type": 'candlestick',
    "valueField": "close",
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
    "labelColorField": "color",
    'axisColor': 'white',
    'color': 'white', 
  },
  "dataProvider": [],
});

function requestData(sel_type, graphType, axis_label, market_type) {
  d = new Date();
  var end = new Date(d.setTime(d.getTime() + (0 * 60 * 60 * 1000))); // now time
  var start = new Date(d.setTime(d.getTime() - (sel_type * 60 * 1000))); // before 5 hours

  var dataProvider = [];	//console.log(sel_type);
  var endstring = end.toISOString();
  var startstring = start.toISOString();
  var url = 'https://api.gdax.com/products/' + market_type + '/candles?granularity=' + sel_type + '&start=' + startstring + '&end=' + endstring;

  AmCharts.loadFile(url, {}, function (point) {
    point = JSON.parse(point);
    point.forEach(function (element, index) {
      year = new Date(parseInt(point[index][0]) * 1000).getYear();
      month = new Date(parseInt(point[index][0]) * 1000).getMonth() + 1;
      day = new Date(parseInt(point[index][0]) * 1000).getDate();
      hour = new Date(parseInt(point[index][0]) * 1000).getHours();
      minutes = new Date(parseInt(point[index][0]) * 1000).getMinutes();
      time = day + " " + hour + ":" + minutes;

      var itemPoint = {
        "date": '' + time,
        "open": point[index][3].toString(),
        "high": point[index][2].toString(),
        "low": point[index][1].toString(),
        "close": point[index][4].toString()
      }
      if (dataProvider.length < 60) {
        dataProvider.push(itemPoint);
      }

    }, this);

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
      chart.graphs[0].fillColors = "#31ff31";
      chart.graphs[0].lineColor = '#31ff31';

    }

    dataProvider.reverse();

    chart.graphs[0].type = graphType;
    chart.allLabels[0].text = "(" + axis_label + ")";
    chart.dataProvider = dataProvider;
    chart.validateData();
    $('#curtain').css('display', 'none');
  });
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

function draw_chart_order(axis_label, market_type) {

  var url = "https://api.gdax.com/products/" + market_type + "/book?level=2";
  var res = []; var min_index = 0; var max_index = 0; var rate_num = parseInt($('#rate_num').val()); var new_index = 0; var lull_num = 10;

  min_index = rate_num * lull_num;
  max_index = 99 - rate_num * lull_num;

  AmCharts.loadFile(url, {}, function (data) {
    data = JSON.parse(data);

    bids_data = processData(data.bids, "bids", true);
    asks_data = processData(data.asks, "asks", false);

    bids_data.push.apply(bids_data, asks_data);

    res = bids_data;
    chart_2.allLabels[0].text = "(" + axis_label + ")";
    chart_2.dataProvider = res;
    chart_2.validateData();
    $('#curtain_2').css('display', 'none');
  });

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
  }, {
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
  }],
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

    $("#price_c").css('color','#fff');
    $("#price_c").css('border-bottom','1px solid #fff');
    $("#depth_c").css('color','hsla(206,8%,82%,.6)');
    $("#depth_c").css('border-bottom','hsla(206,8%,82%,.6)');
  }
  else {
    chart_selection = 'depth';

    $('#chartContain').css('display', 'none');
    $('#chartContain_2').css('display', 'block');

    $("#price_c").css('color','hsla(206,8%,82%,.6)');
    $("#price_c").css('border-bottom','hsla(206,8%,82%,.6)');
    $("#depth_c").css('color','#fff');
    $("#depth_c").css('border-bottom','1px solid #fff');
    
  }
}