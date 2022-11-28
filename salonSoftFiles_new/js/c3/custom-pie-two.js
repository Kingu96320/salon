var chart12 = c3.generate({
	bindto: '#pieChart2',
	data: {
		// iris data from R
		columns: [
			['Sun', 21],
			['Mon', 56],
			['Tue', 78],
			['Wed', 92],
			['Thu', 124],
			['Fri', 145],
			['Sat', 186],
		],
		type : 'pie',
		colors: {
			Sun: '#388348',
			Mon: '#3e924f',
			Tue: '#44a057',
			Wed: '#4aae5f',
			Thu: '#56B76A',
			Fri: '#64bd77',
			Sat: '#73c383',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
});