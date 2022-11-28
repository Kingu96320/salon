$(function () {

	var ds=[], data, chartOptions;

	ds.push ([[2100, 1],[3300, 2],[3900, 3],[4500, 4], [5200, 5], [3100, 6]]);
	ds.push ([[1500, 1],[2200, 2],[2900, 3],[2300, 4], [3800, 5], [1600, 6]]);
	ds.push ([[600, 1],[1300, 2],[1900, 3],[3500, 4], [2700, 5], [3200, 6]]);

	data = [ {
		label: 'News',
		data: ds[1]
	}, {
		label: 'Sports',
		data: ds[0]
	}, {
		label: 'Movies',
		data: ds[2]
	}];

	chartOptions = {
		xaxis: {
				
		},
		grid:{
			hoverable: true,
			clickable: false,
			borderWidth: 1,
			tickColor: '#eaeaea',
			borderColor: '#eaeaea',
		},
		series: {
			stack: true
		},
		legend: {
			position: 'se'
		},
		bars: {
			horizontal: true,
			show: true,
			barWidth: .25,
			fill: true,
			lineWidth: 0,
			fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
		},
		shadowSize: 0,
		tooltip: true,

		tooltipOpts: {
			content: '%s: %x'
		},
		colors: ['#3a86c8', '#64bd63', '#6dc6cd', '#52bf8a', '#638ca5'],
	}

	var holder = $('#stacked-horizontal-chart');
	if (holder.length) {
		$.plot(holder, data, chartOptions );
	}

});