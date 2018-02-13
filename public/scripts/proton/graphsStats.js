$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.graphsStats build');
    
    proton.graphsStats.build();
});

proton.graphsStats = {
	build: function () {
		// Morris Charts
		!(Morris && $('.graph').length) || proton.graphsStats.drawCharts();
		!($('.sparkspan').length) || proton.graphsStats.sparklines();

		setTimeout(function() {
			proton.graphsStats.liveStats();
		}, 2000);

		!verboseBuild || console.log('            proton.graphsStats build DONE');
	},
	randomNum : function (from,to) {
		return Math.floor(Math.random()*(to-from+1)+from);
	},
	statChange : false,
	liveStats : function () {
		if (proton.graphsStats.statChange){
			clearTimeout(proton.graphsStats.statChange);
		}
		var likeOrBuy = proton.graphsStats.randomNum(0,5);
		if (likeOrBuy < 4)
			$('#like-count').text(parseInt($('#like-count').text()) + 1);
		else
			$('#buy-count').text(parseInt($('#buy-count').text()) + 1);

		proton.graphsStats.statChange = setTimeout(proton.graphsStats.liveStats, 500 * proton.graphsStats.randomNum(1,4));
	},
	graph : {},
	redrawCharts : function () {
		!verboseBuild || console.log('            proton.graphsStats.redrawCharts()');

		$.each(proton.graphsStats.graph, function(index, val) {
			this.redraw();
		});
	},
	drawCharts : function () {
		!verboseBuild || console.log('            proton.graphsStats.drawCharts()');

		if($('#myfirstchart').length)
		proton.graphsStats.graph.Line = Morris.Line({
			// ID of the element in which to draw the chart.
			element: 'myfirstchart',
			// Chart data records -- each entry in this array corresponds to a point on
			// the chart.
			data: [
			{ year: '2008', value: 20 },
			{ year: '2009', value: 10 },
			{ year: '2010', value: 5 },
			{ year: '2011', value: 5 },
			{ year: '2012', value: 20 }
			],
			// The name of the data record attribute that contains x-values.
			xkey: 'year',
			// A list of names of data record attributes that contain y-values.
			ykeys: ['value'],
			// Labels for the ykeys -- will be displayed when you hover over the
			// chart.
			labels: ['Value']
		});

		if($('#hero-donut').length)
		proton.graphsStats.graph.Donut = Morris.Donut({
		    element: 'hero-donut',
		    data: [
		      {label: 'Jam', value: 25 },
		      {label: 'Frosted', value: 40 },
		      {label: 'Custard', value: 25 },
		      {label: 'Sugar', value: 10 }
		    ],
		    formatter: function (y) { return y + "%" }
		});

		if($('#hero-bar').length)
		proton.graphsStats.graph.Bar = Morris.Bar({
		    element: 'hero-bar',
		    data: [
		      {device: 'iPhone', geekbench: 136},
		      {device: 'iPhone 3G', geekbench: 137},
		      {device: 'iPhone 3GS', geekbench: 275},
		      {device: 'iPhone 4', geekbench: 380},
		      {device: 'iPhone 4S', geekbench: 655},
		      {device: 'iPhone 5', geekbench: 1571}
		    ],
		    xkey: 'device',
		    ykeys: ['geekbench'],
		    labels: ['Geekbench'],
		    barRatio: 0.4,
		    xLabelAngle: 35,
		    hideHover: 'auto'
		});

		if($('#hero-area').length)
		proton.graphsStats.graph.Area = Morris.Area({
		    element: 'hero-area',
		    data: [
		      {period: '2010 Q1', iphone: 2666, ipad: null, itouch: 2647},
		      {period: '2010 Q2', iphone: 2778, ipad: 2294, itouch: 2441},
		      {period: '2010 Q3', iphone: 4912, ipad: 1969, itouch: 2501},
		      {period: '2010 Q4', iphone: 3767, ipad: 3597, itouch: 5689},
		      {period: '2011 Q1', iphone: 6810, ipad: 1914, itouch: 2293},
		      {period: '2011 Q2', iphone: 5670, ipad: 4293, itouch: 1881},
		      {period: '2011 Q3', iphone: 4820, ipad: 3795, itouch: 1588},
		      {period: '2011 Q4', iphone: 15073, ipad: 5967, itouch: 5175},
		      {period: '2012 Q1', iphone: 10687, ipad: 4460, itouch: 2028},
		      {period: '2012 Q2', iphone: 8432, ipad: 5713, itouch: 1791}
		    ],
		    xkey: 'period',
		    ykeys: ['iphone', 'ipad', 'itouch'],
		    labels: ['iPhone', 'iPad', 'iPod Touch'],
		    pointSize: 2,
		    hideHover: 'auto'
		});
	},
	sparklines: function () {
		!verboseBuild || console.log('            proton.graphsStats.sparklines()');


		function drawMouseSpeedDemo() {
		    var mrefreshinterval = 500; // update display every 500ms
		    var lastmousex=-1; 
		    var lastmousey=-1;
		    var lastmousetime;
		    var mousetravel = 0;
		    var mpoints = [];
		    var mpoints_max = 30;
		    $('html').mousemove(function(e) {
		        var mousex = e.pageX;
		        var mousey = e.pageY;
		        if (lastmousex > -1) {
		            mousetravel += Math.max( Math.abs(mousex-lastmousex), Math.abs(mousey-lastmousey) );
		        }
		        lastmousex = mousex;
		        lastmousey = mousey;
		    });
		    var mdraw = function() {
		        var md = new Date();
		        var timenow = md.getTime();
		        if (lastmousetime && lastmousetime!=timenow) {
		            var pps = Math.round(mousetravel / (timenow - lastmousetime) * 1000);
		            mpoints.push(pps);
		            if (mpoints.length > mpoints_max)
		                mpoints.splice(0,1);
		            mousetravel = 0;
		            $('#mousespeed').sparkline(mpoints, { width: mpoints.length*3, tooltipSuffix: ' pixels per second', lineColor: '#5bc0de', spotColor: '#5bc0de'});
		        }
		        lastmousetime = timenow;
		        setTimeout(mdraw, mrefreshinterval);
		    }
		    // We could use setInterval instead, but I prefer to do it this way
		    setTimeout(mdraw, mrefreshinterval); 
		};

		// MOUSE SPEED CHART
		drawMouseSpeedDemo();

		// PIE CHARTS
		$('.sparkpie').sparkline('html', { type: 'pie', height: '1em', sliceColors: ['#428bca','#5cb85c','#d9534f'] });

		// BAR CHARTS
		$('.sparkbar').sparkline('html', {type: 'bar', barColor: '#428bca', negBarColor: '#d9534f'});

		// COMPOSITE LINE CHARTS, THE SECOND USING VALUES SUPPLIED VIA JAVASCRIPT
		$('#compositeline').sparkline('html', { fillColor: false, lineColor: '#5bc0de', changeRangeMin: 0, chartRangeMax: 10 });
		$('#compositeline').sparkline([4,1,5,7,9,-1,1.5,2,0.5,3,1.5,-5,-1.5,-5,0.5,1.5,1,-1,1.5,2,0.5,5,6,7], 
		    { composite: true, fillColor: false, lineColor: '#d9534f', changeRangeMin: 0, chartRangeMax: 10 });

		// BAR + LINE COMPOSITE CHARTS
		$('#compositebar').sparkline('html', { type: 'bar', barColor: '#428bca', negBarColor: '#d9534f'});
		$('#compositebar').sparkline([4,1,5,7,9,9,8,7,6,6,4,7,8,4,3,2,2,5,6,7], 
		    { composite: true, fillColor: false, lineColor: '#5cb85c', lineWidth: 2 });

		// CUSTOMIZED LINE CHART
		$('#linecustom').sparkline('html', 
		    {height: '1em', width: '3.5em', lineColor: '#428bca', fillColor: '#f0ad4e', 
		    minSpotColor: false, maxSpotColor: false, spotColor: '#d9534f', spotRadius: 3, lineWidth: 3});
	}
}