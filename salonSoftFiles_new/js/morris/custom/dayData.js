// Morris Days
var day_data = [
	{"period": "2016-10-01", "licensed": 3213, "Unify": 887},
	{"period": "2016-09-30", "licensed": 3321, "Unify": 776},
	{"period": "2016-09-29", "licensed": 3671, "Unify": 884},
	{"period": "2016-09-20", "licensed": 3176, "Unify": 448},
	{"period": "2016-09-19", "licensed": 3376, "Unify": 565},
	{"period": "2016-09-18", "licensed": 3976, "Unify": 627},
	{"period": "2016-09-17", "licensed": 2239, "Unify": 660},
	{"period": "2016-09-16", "licensed": 3871, "Unify": 676},
	{"period": "2016-09-15", "licensed": 3659, "Unify": 656},
	{"period": "2016-09-10", "licensed": 3380, "Unify": 663}
];
Morris.Line({
	element: 'dayData',
	data: day_data,
	xkey: 'period',
	ykeys: ['licensed', 'Unify'],
	labels: ['Licensed', 'Unify'],
	resize: true,
	hideHover: "auto",
	gridLineColor: "#e4e6f2",
	pointFillColors:['#ffffff'],
	pointStrokeColors: ['#ff5661'],
	lineColors:['#2d85be', '#e5e8f2', '#ff5661'],
});