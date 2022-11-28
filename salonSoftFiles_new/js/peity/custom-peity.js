// Pie Chart
$(function(){
	$(".pie").peity("pie");
});

// Donut Chart
$(function(){
	$('.donut').peity('donut');
});

// Updating Chart
$(function(){
	var updatingChart = $(".updating-chart").peity("line", {
		width: 240,
		height: 48,
		stroke: "#e25a48",
		fill: false,
		strokeWidth: 5,
	})
	setInterval(function() {
		var random = Math.round(Math.random() * 5)
		var values = updatingChart.text().split(",")
		values.shift()
		values.push(random)
		updatingChart
		.text(values.join(","))
		.change()
	}, 500);
})

// Gradient Bar Chart
$(function(){
	$(".gradientBar").peity("bar", {
		width: 110,
		height: 50,
		fill: function(_, i, all) {
			var g = parseInt((i / all.length) * 255)
			return "rgb(234, " + g + ", 100)"
		}
	})
});

