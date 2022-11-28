$(function () {
	
	var d1, data, chartOptions;

	var d1 = [[1262304000000, 25], [1264982400000, 100], [1267401600000, 1305], [1270080000000, 3129], [1272672000000, 8643], [1275350400000, 2055], [1277942400000, 8062], [1280620800000, 19197], [1283299200000, 21000], [1285891200000, 27000], [1288569600000, 14000], [1291161600000, 19000]];

	data = [{ 
		label: "Facebook", 
		data: d1
	}];

	chartOptions = {
		xaxis: {
			min: (new Date(2009, 11)).getTime(),
			max: (new Date(2010, 11)).getTime(),
			mode: "time",
			tickSize: [1, "month"],
			monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			tickLength: 0
		},
		yaxis: {

		},
		series: {
			lines: {
				show: true, 
				fill: false,
				lineWidth: 2,
			},
			points: {
				show: true,
				radius: 4,
				fill: true,
				fillColor: "#ffffff",
				lineWidth: 2
			}
		},
		 grid:{
			hoverable: true,
			clickable: true,
			borderWidth: 1,
			tickColor: '#eaeaea',
			borderColor: '#eaeaea',
		},
		shadowSize: 0,
		legend: {
			show: true,
			position: 'nw'
		},
		
		tooltip: true,
		tooltipOpts: {
			content: '%s: %y'
		},
		colors: ['#3a86c8', '#64bd63', '#6dc6cd', '#52bf8a', '#638ca5'],
	};

	var holder = $('#line-chart');

	if (holder.length) {
		$.plot(holder, data, chartOptions );
	}
});