$(function () {

	var d1, chartOptions;

	var d1 = [[1260000000000, 12235], [1262000000000, 9235], [1263320000000, 12235], [1264982400000, 13200], [1266320000000, 12235], [1267801600000, 20605], [1270080000000, 16129], [1272672000000, 11643], [1275350400000, 19055], [1277942400000, 30062], [1280620800000, 38197], [1283299200000, 35000], [1285891200000, 27000], [1288569600000, 29000], [1290999000000, 39000]];

	data = [{ 
		label: "Audience Overview", 
		data: d1
	}];
 
	chartOptions = {
		xaxis: {
			min: (new Date(2009, 11, 1)).getTime(),
			max: (new Date(2010, 11, 1)).getTime(),
			mode: "time",
			tickSize: [2, "month"],
			monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			tickLength: 1
		},
		yaxis: {
			ticks: 7
		},
		series: {
			stack: true,
			lines: {
				show: true, 
				fill: true,
				lineWidth: 3,
				fillColor: { colors: [{ opacity: 0.001 }, { opacity: 0.7}] }
			},
			points: {
				show: true,
				radius: 5,
				fill: true,
				fillColor: "#ffffff",
				lineWidth: 3
			}
		},
		grid:{
			hoverable: true,
			clickable: true,
			borderWidth: 1,
			tickColor: '#eaeaea',
			borderColor: '#eaeaea',
		},
		legend: {
			show: true,
			position: 'nw'
		},
		shadowSize: 0,
		tooltip: true,
		tooltipOpts: {
		content: '%s: %y'
		},
		colors: ['#3b76a0', '#009688'],
	};

	var holder = $('#area-chart3');

	if (holder.length) {
		$.plot(holder, data, chartOptions );
	}

});