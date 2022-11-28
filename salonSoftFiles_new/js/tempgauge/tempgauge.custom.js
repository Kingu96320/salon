$(function(){
	if(!(/^\?noconvert/gi).test(location.search)){
		$(".tempGauge0").tempGauge({width: 90, borderWidth: 4, borderColor: "#ff7671", fillColor: "#ff7671", showLabel: true});
		$(".tempGauge1").tempGauge({width: 70, borderWidth: 4, borderColor: "#ffda68", fillColor: "#ffda68", showLabel: true});
		$(".tempGauge2").tempGauge({width: 50, borderWidth: 4, borderColor: "#3fcbca", fillColor: "#3fcbca", showLabel: true});
		$(".tempGauge3").tempGauge({width: 40, borderWidth: 4, borderColor: "#4bb5ea", fillColor: "#4bb5ea", showLabel: true});	
	}
});