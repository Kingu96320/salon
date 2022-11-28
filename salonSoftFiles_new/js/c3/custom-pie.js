var chart9 = c3.generate({
	bindto: '#pieChart',
	data: {
		// iris data from R
		columns: [
			['Likes', 36],
			['Shares', 62],
		],
		type : 'pie',
		colors: {
			Likes: '#f35454',
			Shares: '#3a86c8',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	}
});