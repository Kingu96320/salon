var chart6 = c3.generate({
	bindto: '#barGraphOne',
	data: {
		columns: [
			['lead1', 15, 38, 62],
			['lead2', 21, 26, 30],
			['lead3', 9, 32, 48],
			['lead4', 19, 41, 87],
		],
		type: 'bar',
		names: {
			lead1: 'Lead A',
			lead2: 'Lead B',
			lead3: 'Lead C',
			lead4: 'Lead D'
		},
		colors: {
			lead1: '#ff6a5d',
			lead2: '#009de4',
			lead3: '#8bc34a',
			lead4: '#f5c732'
		},
	},
	grid: {
		x: {
			show: true
		},
		y: {
			show: true
		}
	}
});