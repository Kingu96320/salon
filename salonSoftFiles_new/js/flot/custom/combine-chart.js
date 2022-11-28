$(function () {    
	var data1 = [
		[1354586000000, 120], [1364587000000, 60], [1374588000000, 367],
		[1384589000000, 580], [1394590000000, 660], [1404591000000, 487],
		[1414592000000, 920], [1424593000000, 1089], [1434594000000, 1121],
		[1444595000000, 1398], [1454596000000, 1028], [1464597000000, 1664]
	];

	var data2 = [
		[1354586000000, 93], [1364587000000, 85], [1374588000000, 158],
		[1384589000000, 113], [1394590000000, 280], [1404591000000, 398],
		[1414592000000, 480], [1424593000000, 320], [1434594000000, 933],
		[1444595000000, 1019], [1454596000000, 838], [1464597000000, 1490]
	];

	var data = [{
		label: "Sales",
		data: data1,
		bars: {
			show: true,
			lineWidth: 0,
			barWidth: 30 * 60 * 60 * 1000 * 80,
			fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
		}
	},{
		label: "Expenses",
		data: data2,
		lines: {
			show: true,
			lineWidth: 2,
			fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
		},
		points:{
			show:true,
			radius: 4,
			fill: true,
			fillColor: "#ffffff",
			lineWidth: 2
		}
	}];

	var options = {
		series: {
		shadowSize: 0,
		bars: {
			lineWidth: 2,
			fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
		}
	},
	grid: {
		hoverable: true,
		clickable: false,
		borderWidth: 1,
		tickColor: '#eaeaea',
		borderColor: '#eaeaea',
	},
	legend:{   
		show: true,
		position: 'nw',
		noColumns: 0,
	},
	tooltip: true,
	tooltipOpts: {
		content: '%x: %y'
	},

	xaxis: {mode: "time", ticks:6, tickDecimals: 0},
	yaxis: {ticks:6, tickDecimals: 0},

	colors: ['#3a86c8', '#64bd63', '#6dc6cd', '#52bf8a', '#638ca5'],
};

var plot = $.plot($("#combineChart"), data, options);  
});