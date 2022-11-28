var chart25 = c3.generate({
	bindto: '#pieChart4',
	data: {
		// iris data from R
		columns: [
			['Sun', 12],
			['Mon', 26],
			['Tue', 38],
			['Wed', 43],
			['Thu', 67],
			['Fri', 98],
			['Sat', 145],
		],
		type : 'pie',
		colors: {
			Sun: '#f63407',
			Mon: '#f8431a',
			Tue: '#f9542d',
			Wed: '#f96441',
			Thu: '#FA7455',
			Fri: '#fb8469',
			Sat: '#fb947d',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
});