$('#defaultBar').barIndicator();



// Bar Color
var opt = {foreColor:'#ff7671'};$('#barColor').barIndicator(opt);

// Bar Height
var opt = { horBarHeight:27}; $('#barHeight').barIndicator(opt);

// Vertical Height
var opt = { style:'vertical'}; $('#barVertical').barIndicator(opt);
var opt = { style:'vertical', foreColor:'#ff7671'}; $('#barVertical2').barIndicator(opt);
var opt = { style:'vertical', foreColor:'#23a03e'}; $('#barVertical3').barIndicator(opt);

// Bar Holder Color
var opt = {foreColor:'#ffb400', backColor:'#3693cf'}; $('#barHolderColor').barIndicator(opt);

// label Positions
var opt = {horLabelPos:'topRight', foreColor:'#ff7671'};$('#barLabelTopRight').barIndicator(opt);
var opt = {horLabelPos:'right', foreColor:'#3693cf'};$('#barLabelRight').barIndicator(opt);
var opt = {horLabelPos:'left', foreColor:'#23a03e'};$('#barLabelLeft').barIndicator(opt);