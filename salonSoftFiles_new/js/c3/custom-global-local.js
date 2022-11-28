var chart5 = c3.generate({
	bindto: '#globalLocal',
	data: {
		columns: [
			['data2', 15, 19, 27, 32, 38, 36, 32, 36, 40, 48, 46, 53, 58, 62, 65, 99],
		],
		types: {
			data2: 'area-spline'
		},
		names: {
			data2: 'Active Users'
		},
		colors: {
			data2: '#52bf8a'
		},
	},
	axis: {
		x: {
			show: false
		},
		y: {
			show: false
		},
	}
});