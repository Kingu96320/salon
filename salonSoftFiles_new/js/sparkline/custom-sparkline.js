// sparkline Graphs
$(function() {
	$('#invoice').sparkline([2,2,3,4,5,6,7,11,10,6,12,4,8,14,11,3,7], {
		type: 'bar',
		barColor: '#ff7671',
		barWidth: 7,
		barSpacing: 2,
		height: 30,
	});
	$('#invoice').sparkline([1,1,2,2,3,3,4,7,5,5,3,4,3,7,8,4,6,9,12,11], {
		composite: true,
		fillColor: false,
		type: 'line',
		lineColor: '#000000',
		lineWidth: 1,
		height: 30,
	});
});

$(function() {
	$("#moneySpend").sparkline([58,68,78,88,58], {
		type: 'bullet',
		height: '16',
		targetColor: '#4bb5ea',
		performanceColor: '#ffd0c5',
		rangeColors: ['#ff7671', '#CC8271','#FF9878'],
	});
});

$(function() {
	$("#barGraph").sparkline([4,5,6,7,8,9,10,11,10,9,8,7,6,5,4], {
		type: 'bar',
		height: '70',
		barWidth: 7,
		barSpacing: 5,
		barColor: '#4bb5ea'
	});
});

$(function() {
	$("#barNegativeGraph").sparkline([2,-1,3,-5,4,7,-3,4,2,-3,4,-1,4], {
		type: 'bar',
		height: '70',
		barWidth: 7,
		barSpacing: 2,
		barColor: '#4bb5ea',
		negBarColor: '#ffda68'
	});
});

$(function(){
	$("#discreteGraph").sparkline([4,6,7,7,4,3,2,1,4,4], {
		type: 'discrete',
		width: '120',
		height: '70',
		lineColor: '#4bb5ea'
	});
});

$(function(){
	$("#bulletGraph").sparkline([10,12,12,9,7], {
		type: 'bullet',
		height: '20',
		width: '90',
		targetColor: '#ff7671',
		performanceColor: '#625750',
		rangeColors: ['#96897f', '#c6bcb6','#e0e2e4']
	});
});

$(function(){
	$("#pieGraph").sparkline([1,1,2], {
		type: 'pie',
		width: '70',
		height: '70',
		sliceColors: ['#4bb5ea','#3fcbca','#ff7671']
	});
});

$(function(){
	$("#boxGraph").sparkline([4,27,34,52,54,59,61,68,78,82,85,87,91,93,100], {
		type: 'box',
		width: '120 ',
		height: '60',
		boxLineColor: '#4bb5ea',
		boxFillColor: '#4bb5ea',
		whiskerColor: '#4bb5ea',
		outlierLineColor: '#4bb5ea',
		medianColor: '#ffffff',
		targetColor: '#3fcbca'
	});
});

$(function(){
	$('#compositeBar').sparkline([4,5,6,7,8,9,10,11,10,9,8,7,6,5,4], {
		type: 'bar',
		barColor: '#3fcbca',
		width: '120 ',
		height: '60',
		barSpacing: 5,
	});
	$('#compositeBar').sparkline([3,2,4,3,5,4,3,5,4,4,7,9,12,15,12], {
		composite: true,
		fillColor: false,
		lineColor: '#4bb5ea',
		width: '120 ',
		height: '60',
		lineWidth: 2,
		spotRadius: 5
	});
});

$(function(){
	$('#compositeLine').sparkline([4,1,5,7,9,9,8,7,6,6,4,7,8,4,3,2,2,5,6,7], {
		type: 'line',
		lineColor: '#ff7671',
		width: '120 ',
		height: '50',
		lineWidth: 2,
		spotRadius: 3,
		fillColor: false
	});
	$('#compositeLine').sparkline([8,4,0,0,0,0,1,4,4,10,10,10,10,0,0,0,4,6,5,9,10], {
		type: 'line',
		fillColor: false,
		composite: true,
		fillColor: false,
		lineColor: '#4bb5ea',
		width: '120 ',
		height: '50',
		lineWidth: 2,
		spotRadius: 3
	});
});



$(function(){
	$('#rowOne').sparkline([320,250,400,380,280,320,220,385,450], {
		type: 'line',
		lineWidth: 2,
		fillColor: false,
		lineColor: '#ff7671',
		spotColor: '#50B432',
		minSpotColor: '#f7b53c',
		maxSpotColor: '#F782AA',
		highlightSpotColor: '#',
		height: 20,
		width: 80,
		spotRadius: 3,
	});
});

$(function(){
	$('#rowTwo').sparkline([230,210,315,190,250,200,330,280,350], {
		type: 'line',
		lineWidth: 2,
		fillColor: false,
		lineColor: '#3fcbca',
		spotColor: '#50B432',
		minSpotColor: '#f7b53c',
		maxSpotColor: '#F782AA',
		highlightSpotColor: '#',
		height: 20,
		width: 80,
		spotRadius: 3,
	});
});

$(function(){
	$('#rowThree').sparkline([280,320,220,385,450,320,250,400,280], {
		type: 'line',
		lineWidth: 2,
		fillColor: false,
		lineColor: '#4bb5ea',
		spotColor: '#50B432',
		minSpotColor: '#f7b53c',
		maxSpotColor: '#F782AA',
		highlightSpotColor: '#',
		height: 20,
		width: 80,
		spotRadius: 3,
	});
});
