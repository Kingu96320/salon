// Morris Bar No Axis
Morris.Bar({
	element: 'barNoAxis',
	axes: false,
	data: [
		{x: '2017 Q1', y: 3, z: 2, a: 3},
		{x: '2017 Q2', y: 2, z: 1, a: 1},
		{x: '2017 Q3', y: 5, z: 2, a: 4},
		{x: '2017 Q4', y: 2, z: 4, a: 3}
	],
	xkey: 'x',
	ykeys: ['y', 'z', 'a'],
	labels: ['Y', 'Z', 'A'],
	resize: true,
	hideHover: "auto",
	gridLineColor: "#e4e6f2",
	barColors:['#2d85be', '#e5e8f2', '#ff5661'],
});