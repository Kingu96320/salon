var chart13 = c3.generate({
	bindto: '#domain',
	data: {
		columns: [
			['Com', 100],
			['Org', 20],
			['Net', 15],
			['Biz', 10],
			['Others', 5],
		],
		type : 'pie',
		colors: {
			Com: '#fa9255',
			Org: '#fee074',
			Net: '#47BCC7',
			Biz: '#F782AA',
			Others: '#aed048',
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