var chart12 = c3.generate({
	bindto: '#domestic',
	data: {
		columns: [
			['Domestic', 70],
			['International', 120],
		],
		type : 'pie',
		colors: {
			Domestic: '#E24B46',
			International: '#1d73bd',
		},
	},
	legend: {
		show: false
	},
	tooltip: {
		show: false
	},
	pie: {
  	label: {
    	show: false
  	}
	},
});