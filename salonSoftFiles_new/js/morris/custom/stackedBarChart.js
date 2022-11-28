// Stacked Bar Chart
Morris.Bar({
	element: 'stackedBarChart',
	data: [
		{x: '2016 Q1', y: 3, z: 2, a: 3},
		{x: '2016 Q2', y: 2, z: null, a: 1},
		{x: '2016 Q3', y: 0, z: 2, a: 4},
		{x: '2016 Q4', y: 2, z: 4, a: 3}
	],
	xkey: 'x',
	ykeys: ['y', 'z', 'a'],
	labels: ['Y', 'Z', 'A'],
	stacked: true,
	hideHover: "auto",
	resize: true,
	gridLineColor: "#e4e6f2",
	barColors:['#2d85be', '#e5e8f2', '#ff5661'],
});