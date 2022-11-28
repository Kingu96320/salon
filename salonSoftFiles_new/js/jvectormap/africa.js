// Denmark
$(function(){
	$('#mapAfrica').vectorMap({
		map: 'africa_mill',
		backgroundColor: '#2797e2',
		scaleColors: ['#707C8E'],
		zoomOnScroll: false,
		zoomButtons : false,
		zoomMin: 1,
		hoverColor: true,
		series: {
			regions: [{
				values: gdpData,
				scale: ['#2797e2', '#ffd570', '#23a03e', '#E4817B', '#AE4357'],
				normalizeFunction: 'polynomial'
			}]
		},
		markerStyle: {
			initial: {
				fill: '#e83232',
				stroke: '#FFFFFF',
				r: 10
			}
		},
		markers :[
			{latLng: [-33.9249, 18.4241], name: 'Cape Town, South Africa'},
		],
	});
});