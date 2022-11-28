$( document ).ready(function() {
	$("#direct").circliful({
		animation: 1,
		animationStep: 5,
		foregroundBorderWidth: 20,
		backgroundBorderWidth: 10,
		percent: 78,
		fontColor: '#000000',
		foregroundColor: '#e25a48',
		backgroundColor: '#dddddd',
		multiPercentage: 1,
		percentages: [10, 20, 30]
	});
	$("#referrals").circliful({
		animation: 1,
		animationStep: 5,
		foregroundBorderWidth: 20,
		backgroundBorderWidth: 10,
		percent: 43,
		fontColor: '#000000',
		foregroundColor: '#ffb400',
		backgroundColor: '#dddddd',
		multiPercentage: 1,
		percentages: [10, 20, 30]
	});
	$("#search-engines").circliful({
		animation: 1,
		animationStep: 5,
		foregroundBorderWidth: 20,
		backgroundBorderWidth: 10,
		percent: 29,
		fontColor: '#000000',
		foregroundColor: '#74b749',
		backgroundColor: '#dddddd',
		multiPercentage: 1,
		percentages: [10, 20, 30]
	});

	$("#sessions").circliful({
		animationStep: 5,
		foregroundBorderWidth: 5,
		backgroundBorderWidth: 15,
		percent: 39,
		fontColor: '#000000',
		foregroundColor: '#e25a48',
		backgroundColor: '#dddddd',			
		icon: 'e094',
		iconColor: '#e25a48',
		iconPosition: 'middle',
		textBelow: false
	});

	$("#users").circliful({
		animationStep: 5,
		foregroundBorderWidth: 5,
		backgroundBorderWidth: 15,
		percent: 87,
		fontColor: '#000000',
		foregroundColor: '#ffc510',
		backgroundColor: '#dddddd',			
		icon: 'e63d',
		iconColor: '#ffc510',
		iconPosition: 'middle',
		textBelow: false
	});

	$("#duration").circliful({
		animationStep: 5,
		foregroundBorderWidth: 5,
		backgroundBorderWidth: 15,
		percent: 66,
		fontColor: '#000000',
		foregroundColor: '#74b749',
		backgroundColor: '#dddddd',			
		icon: 'e014',
		iconColor: '#74b749',
		iconPosition: 'middle',
		text: 'Duration',
		textBelow: true
	});	
	
	$("#bounce-rate").circliful({
		animationStep: 5,
		foregroundBorderWidth: 5,
		backgroundBorderWidth: 15,
		percent: 21,
		fontColor: '#000000',
		foregroundColor: '#4bb5ea',
		backgroundColor: '#dddddd',			
		icon: 'e87e',
		iconColor: '#4bb5ea',
		iconPosition: 'middle',
		text: 'Bounce Rate',
		textBelow: true
	});

});

