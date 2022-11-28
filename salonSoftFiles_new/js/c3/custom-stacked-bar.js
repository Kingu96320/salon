var chart7 = c3.generate({
	bindto: '#stackedBarGraph',
	data: {
	columns: [
		['data1', 30, 90, 200, 400, 590, 250, 330, 120, 480, 560, 220, 300, 240, 470, 680, 550, 250, 330, 120, 90],
		// ['data2', 130, 100, 200, 200, 450, 150, 190, 220, 350, 180, 330, 550, 280, 180, 200, 450, 150, 190, 220],
		// ['data3', 230, 200, 200, 300, 250, 250, 320, 180, 410, 270, 180, 210, 270, 420, 330, 180, 410, 270, 180, 110]
	],
	type: 'bar',
	names: {
		data1: 'Visits',
		// data2: 'LinkedIn',
		// data3: 'Facebook',
	},
	colors: {
		data1: '#ea423f',
		// data2: '#E24B46',
		// data3: '#dddddd',
	},
	groups: [
		['data1']
	]
	},
	grid: {
		x: {
			show: true,
		},
		y: {
			show: true
		}
	}
});