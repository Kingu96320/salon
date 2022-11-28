var chart14 = c3.generate({
	bindto: '#imgFonts',
	data: {
		columns: [
			['Images', 170],
			['Fonts', 50],
			['Text', 30],
		],
		type : 'pie',
		colors: {
			Images: '#5c6bc2',
			Fonts: '#C790E1',
			Text: '#F782AA',
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