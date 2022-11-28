var chart24 = c3.generate({
	bindto: '#pieChart3',
	data: {
		// iris data from R
		columns: [
			['Sun', 66],
			['Mon', 89],
			['Tue', 94],
			['Wed', 112],
			['Thu', 129],
			['Fri', 157],
			['Sat', 300],
		],
		type : 'pie',
		colors: {
			Sun: '#2da0c4',
			Mon: '#34abd1',
			Tue: '#45b2d4',
			Wed: '#55b9d8',
			Thu: '#66C0DC',
			Fri: '#77c7e0',
			Sat: '#87cee4',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
});