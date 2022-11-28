// Morris Negative values
var neg_data = [
		{"period": "2017-02-12", "a": 100},
		{"period": "2017-01-03", "a": 75},
		{"period": "2016-08-08", "a": 50},
		{"period": "2016-05-10", "a": 25},
		{"period": "2016-03-14", "a": 0},
		{"period": "2016-01-10", "a": -25},
		{"period": "2005-12-10", "a": -50},
		{"period": "2005-10-07", "a": -75},
		{"period": "2005-09-25", "a": -100}
];
Morris.Line({
	element: 'negativeValues',
	data: neg_data,
	xkey: 'period',
	ykeys: ['a'],
	labels: ['Series A'],
	units: '%',
	resize: true,
	hideHover: "auto",
	gridLineColor: "#e4e6f2",
	pointFillColors:['#ffffff'],
	pointStrokeColors: ['#ff5661'],
	lineColors:['#2d85be', '#e5e8f2', '#ff5661'],
});