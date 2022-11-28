var chart10 = c3.generate({
	bindto: '#donutChart',
	data: {
		columns: [
			['Likes', 32],
			['Shares', 87],
			['Clicks', 51],
		],
		type : 'donut',
		colors: {
			Likes: '#ea423f',
			Shares: '#f35454',
			Clicks: '#3b5998',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	donut: {
		title: "Clicks"
	},
});