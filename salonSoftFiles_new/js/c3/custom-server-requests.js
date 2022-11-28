var chart11 = c3.generate({
	bindto: '#serverRequests',
	padding: {
		top: 10,
		left: 40,
	},
	data: {
		columns: [
			['data1', 18, 22, 90, 33, 19, 21, 28, 21, 19, 43, 23, 34, 55, 43, 33, 77, 33, 87, 46, 39, 51, 32, 66, 99, 32, 54, 33, 24, 54, 22, 37, 76, 67, 89, 34, 12, 77, 99, 59, 66, 28, 77, 39, 60,  66, 99, 32, 54, 33, 24, 54, 22, 37, 76, 67, 89, 34, 12, 77, 99, 59, 66, 28, 77, 39, 60],
		],
		types: {
			data1: 'area',
		},
		names: {
			data1: 'Requests',
		},
		colors: {
			data1: '#c38fbb',
		},
	},
	axis: {
		y: {
			tick: {
				count: 3,
			}
		}
	}
});