<?php
include "./includes/db_include.php";

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<link rel="shortcut icon" href="img/favicon.ico">
		<title>Easy Salon & Spa Software</title>
		
		<!-- Bootstrap CSS -->
		<link href="css/bootstrap.min.css" media="screen" rel="stylesheet" />

		<!-- Main CSS -->
		<link href="css/main.css" rel="stylesheet" media="screen" />

		<!-- Ion Icons -->
		<link href="css/timepicker.css" rel="stylesheet" />
		<link href="fonts/icomoon/icomoon.css" rel="stylesheet" />
		
		<!-- C3 CSS -->
		<link href="css/c3/c3.css" rel="stylesheet" rel="stylesheet" />

		<!-- Circliful CSS -->
		<link href="css/circliful/circliful.css" rel="stylesheet" />

		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		<!-- Calendar CSS -->
		<script src="js/jquery.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<link rel="stylesheet" href="site/css/bootstrap.css" type="text/css" />
		
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<link href="css/calendar/fullcalendar.min.css" rel="stylesheet" />
		<link href="css/calendar/custom-calendar.css" rel="stylesheet" />
		 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/jquery.form.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
		
		 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0,fh-3.1.0,sc-1.4.0/datatables.min.css">
      <script type="text/javascript" src="https://cdn.datatables.net/s/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0,fh-3.1.0,sc-1.4.0/datatables.min.js"></script>
		
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/js/toastr.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/css/toastr.css">

<style>
.wizard {
    margin: 20px auto;
    background: #fff;
}

    .wizard .nav-tabs {
        position: relative;
        margin: 40px auto;
        margin-bottom: 0;
        border-bottom-color: #e0e0e0;
    }

    .wizard > div.wizard-inner {
        position: relative;
    }

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 80%;
    margin: 0 auto;
    left: 0;
    right: 0;
    top: 50%;
    z-index: 1;
}

.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
    color: #555555;
    cursor: default;
    border: 0;
    border-bottom-color: transparent;
}

span.round-tab {
    width: 70px;
    height: 70px;
    line-height: 70px;
    display: inline-block;
    border-radius: 100px;
    background: #fff;
    border: 2px solid #e0e0e0;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 25px;
}
span.round-tab i{
    color:#555555;
}
.wizard li.active span.round-tab {
    background: #fff;
    border: 2px solid #5bc0de;
    
}
.wizard li.active span.round-tab i{
    color: #5bc0de;
}

span.round-tab:hover {
    color: #333;
    border: 2px solid #333;
}

.wizard .nav-tabs > li {
    width: 25%;
}

.wizard li:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 0;
    margin: 0 auto;
    bottom: 0px;
    border: 5px solid transparent;
    border-bottom-color: #5bc0de;
    transition: 0.1s ease-in-out;
}

.wizard li.active:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 1;
    margin: 0 auto;
    bottom: 0px;
    border: 10px solid transparent;
    border-bottom-color: #5bc0de;
}

.wizard .nav-tabs > li a {
    width: 70px;
    height: 70px;
    margin: 20px auto;
    border-radius: 100%;
    padding: 0;
}

    .wizard .nav-tabs > li a:hover {
        background: transparent;
    }

.wizard .tab-pane {
    position: relative;
    padding-top: 50px;
}

.wizard h3 {
    margin-top: 0;
}

@media( max-width : 585px ) {

    .wizard {
        width: 90%;
        height: auto !important;
    }

    span.round-tab {
        font-size: 16px;
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard .nav-tabs > li a {
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard li.active:after {
        content: " ";
        position: absolute;
        left: 35%;
    }
}
</style>
		
	</head>
	<?php $toast = $_SESSION['t']; ?>
	<body <?php if($toast>0){ echo 'onload="myalert()"'; } ?> >

<script type="text/javascript">
var sno = 1;
   $(function() {
  $("#btnAdd").bind("click", function() {
    var tr = $("<tr />");
    tr.html(GetDynamicTextBox(""));
	var clonetr = $("#TextBoxContainer").clone();
	clonetr.removeAttr('id');
	clonetr.find('.sno').html((++sno)+'<span class="remm icon-trash2" style="color:red;" onclick="$(this).parent().parent().remove();"></span>');
	clonetr.find('input').val('');
	
    $("#addBefore").before(clonetr);
	autocomplete_serr();
  });
  $("#btnGet").bind("click", function() {
    var values = 
      $.map($("input[name=DynamicTextBox]"), function(el) {
        return el.value
      }).join(",\n");
    $("#anotherTextbox").val(values);
  });
  $("body").on("click", ".remove", function() {
    $(this).closest("tr").remove();
  });
});

function GetDynamicTextBox(value) {
  return '<tr><td>001 <span class="icon-trash2" style="color:red;"></span></td>'+
		'<td><input type="text" class="form-control" id="userName" placeholder="Service (Autocomplete)"></td>'+
		'<td><input type="text" class="form-control" id="userName" value="1"></td>'+
		'<td><input type="text" class="form-control" id="userName" placeholder="9800.00" readonly></td>'+
		'<td><input type="button" value="X" class="remove btn btn-warning col-lg-2" style="width: 40px;" /><td>'
}
</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("keyup", ".ser", function () {
    var $row = $(this).closest(".ser");
	 var row = $(this).parent().parent();
$.ajax({
url: "getservice.php?ser="+$(this).val(),
type: "POST",
success:function(data){
	var ds = JSON.parse(data.trim());
row.find('.serr').val(ds[0]['id']);
row.find('.durr').val(ds[0]['durr']);
row.find('.pr').val(ds[0]['price']);
row.find('.prr').val(ds[0]['price']);
row.find('.qt').val('1');
	var pr = row.find('.prr').val();
	 var qt = row.find('.qt').val();
	 
	 var sum = pr * qt;
	 row.find('.price').val(sum);
	 sumup();

},
error:function (){}
});
	 
});
});
</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("blur", ".ser", function () {
	//alert("running");
    var $row = $(this).closest(".ser");
	 var row = $(this).parent().parent();
$.ajax({
url: "checkser.php?ser="+$(this).val(),
type: "POST",
success:function(data){
	var ds = JSON.parse(data.trim());
	row.find('.serr').val(ds[0]['id']);
row.find('.pr').val(ds[0]['price']);
row.find('.prr').val(ds[0]['price']);

	var pr = row.find('.prr').val();
	 var qt = row.find('.qt').val();
	 var sum = pr * qt;
	 row.find('.price').val(sum);
	 row.find('.qt').focus();
	 sumup();
	 
if ( data.indexOf("[\"No Column Found\"]") > -1 ) {
		row.find('.ser').val("");
}
},
error:function (){}
});
	 
});
});
</script>
		<div class="dashboard-wrapper">
			<div class="main-container">
				<div class="row gutter">	
					<div class="container">
	<div class="row">
		<section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1" id="tab1">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-user"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2" id="tab2">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-time"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3" id="tab3">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-briefcase"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete" id="tab4">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-ok"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <form role="form" id="form">
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1">
                        <h3>Step 1</h3>
								<div class="row">
									<div class="col-lg-3col-md-2 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">Client Name</label>
												<input type="text" class="client form-control" id="client" name="client" placeholder="john " required><input type="hidden" name="clientid" id="clientid" class="clt"> 
												<span id="client-status"></span>
											</div>
									</div>
									
									<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
											<div class="form-group">
												<label for="userName">Contact Number</label>
												<input type="text" class="form-control" id="cont" name="cont" placeholder="Client Contact" required>
											</div>
									</div>
									
									<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
											<div class="form-group">
												<label for="userName">Gender</label>
												<select name="gender" id="gender" data-validation="required" class="form-control">
													<option>Male</option>
                                                    <option>Female</option>
												</select>
											</div>
									</div>
									<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
											<div class="form-group">
												<label for="userName">Date of Birth</label>
												<input type="text" class="form-control date" id="dob" name="dob" placeholder="Client Contact" required readonly>
											</div>
									</div>
								</div>
                        <ul class="list-inline pull-right">
                            <li><button type="button" class="btn btn-primary" onclick="checkuser();">Save and continue</button><button type="button" class="next-step" id="btn1" style="display:none;">Save and continue</button></li>
                        </ul>
                    </div>
					<script>
					function checkuser(){
						var client = $("#client").val();
						var cont = $("#cont").val();
						//alert(client + cont)
						if(client==""||cont==""){
							alert("Please Fill all Fields");
						}else{
							$('#btn1').trigger('click');
						}	
					}
					</script>
                    <div class="tab-pane" role="tabpanel" id="step2">
                        <h3>Step 2</h3>
						<div class="row">
						<div class="col-lg-2"></div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
											<?php $date = date('Y-m-d'); ?>
												<label for="userName">Date of appointment</label>
												<input type="text" class="form-control date dat" id="date" value="<?php echo $date; ?>" name="doa" placeholder="Autocomplete (Phone)" readonly>
											</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group" id="datetimepicker3">
											<?php $time = date('H:i'); ?>
												<label for="userName">Time</label>
												<input type='text' class="form-control timepicker slot dat" name="time" value="<?php echo $time; ?>" id="time" placeholder="Autocomplete (Phone)" required>	
											</div>
									</div>
							</div>
							<script type="text/javascript" src="js/timepicker.js"></script>
							<script>
							$('#time').datetimepicker({
								format: 'LT',
								showClose: true
							});
							</script>
                        <ul class="list-inline pull-right">
                            <li><button type="button" class="btn btn-default prev-step" onclick="$('#tab1').trigger('click');">Previous</button></li>
                            <li><button type="button" class="btn btn-primary next-step" onclick="$('#tab3').trigger('click');">Save and continue</button></li>
                        </ul>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step3">
                        <h3>Step 3</h3>
                        <table id="myTable" class="table table-bordered">
														<thead>
															<tr>
																<th style="width:10%">Sl.No.</th>
																<th style="width:20%">Service</th>
																<th style="width:10%">Quantity</th>
																<th style="width:10%" hidden>Price</th>
															</tr>
														</thead>
														<tbody>
															<tr id="TextBoxContainer">
																<td class="sno">1</td>
																<td>
                                                                    <input type="text" class="ser form-control slot" name="services[]" placeholder="Service (Autocomplete)" required>
                                                                    <input type="hidden" name="service[]" class="serr">
                                                                    <input type="hidden" name="durr[]" class="durr">
                                                                </td>
																<td><input type="number" name="qt[]" class="qt form-control sal slot" name="quantity[]" value="1"></td>
																<td hidden><input type="text" class="pr form-control price" name="price[]" id="userName" placeholder="9800.00" readonly>
																<input type="hidden" class="prr"></td>
															</tr>
															<tr id="addBefore">
																<td colspan="4"><button type="button" id="btnAdd" class="btn btn-info pull-right">Add Row</button></td>
															</tr>
															<tr hidden>
																<td class="total" colspan="3">Services Worth</td>
																<td id="sum">0</td>
															</tr>
														</tbody>
													</table>
                        <ul class="list-inline pull-right">
                            <li><button type="button" class="btn btn-default prev-step" onclick="$('#tab2').trigger('click');">Previous</button></li>
                            <li><button type="button" class="btn btn-primary btn-info-full" onclick=" checkservice();">Save and continue</button><button type="button" class="next-step" id="btn4" style="display:none;"></button></li>
                        </ul>
                    </div>
					<script>
					function checkservice(){
						var error = false;
						$('.ser').each(function(){
							if($(this).val()==''){
								error = true;
							}
						});
						if(error==true){
							alert("Please Fill all Fields");
							
						}else{
							saveform();
						}
					}
					</script>
					
                    <div class="tab-pane" role="tabpanel" id="complete">
                        <h3>Complete</h3>
                        <p>Your Appointment is successfully Recieved.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </section>
   </div>
</div>
				</div>
			</div>
		</div>
		<!-- Container fluid ends -->		

<script>
function saveform(){
var myform = document.getElementById("form");
    var fd = new FormData(myform );
    $.ajax({
        url: "save.php",
        data: fd,
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (dataofconfirm) {
			toastr.success(dataofconfirm);
			$('#btn4').trigger('click');
            //toastr.success("Appointment Received Successfully.");
        }
    });
}
</script>		

		
<script type="text/javascript">
		$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}		
	</script>
	
	<script type="text/javascript">
		$(function() {
			autocomplete_serr();										
		});
		function autocomplete_serr(){
			$(".ser").autocomplete({
				source: "service.php",
				minLength: 1
		});	
		}
	</script>

<script>
$(document).on("blur", '.dat', function() {

var TodayDate = new Date().getTime();
var dat = $("#date").val();
var tim = $("#time").val();
var endDate= new Date(Date.parse($("#date").val()));
var d = mysqlDate();
var t = new Date().getHours();
var m = new Date().getMinutes();
var tt = t+":"+m.toPrecision(2);

if (endDate > TodayDate) {
   
}else if(endDate < TodayDate){
	 //$("#date").val(d);
	 if(tim < tt){
		//alert(tt);
		$('#time').val(tt);
	 }
	 
}else if(endDate == TodayDate){
	
}
});

function mysqlDate(date){
    date = date || new Date();
    return date.toISOString().split('T')[0];
}
</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("keyup", ".qt", function () {
    var $row = $(this).closest(".qt");
	 var row = $(this).parent().parent();
	 var pr = row.find('.prr').val();
	 var qt = $(this).val();
	 var sum = pr * qt;
	 row.find('.price').val(sum);
	 var pric = 0;
	 var  sum = 0;
	 var inputs = $(".price");
	 for(var i = 0; i < inputs.length; i++){
		 sum = parseInt(sum) + parseInt($(inputs[i]).val());
		 $("#sum").html("Rs. "+sum);
	}
});
});
</script>

<script type='text/javascript'>//<![CDATA[
$(".key").keyup(function(event){
		//alert("keyup");
		sumup();
  });
  
function sumup(){
	
	 var row = $(this).parent().parent();
	 //alert("sumup"); 
	 var pric = 0;
	 var  sum = 0;
	 var inputs = $(".price");
	 for(var i = 0; i < inputs.length; i++){
		 sum = parseInt(sum) + parseInt($(inputs[i]).val());
		 $("#sum").html("Rs. "+sum);
	}
}
</script>

<script type='text/javascript'>//<![CDATA[
$(document).on("click", '#tax', function() {
		
		sumup();
	
});
</script>

<script type='text/javascript'>//<![CDATA[
$(document).on("click", '.remm', function() {
  
  var $row = $(this).closest(".qt");
	 var row = $(this).parent().parent();
	 var pr = row.find('.prr').val();
	 var qt = $(this).val() ;
	 var sum = pr * qt;
	 row.find('.price').val(sum);

	 var pric = 0;
	 var  sum = 0;
	 var inputs = $(".price");
	 for(var i = 0; i < inputs.length; i++){
		 sum = parseInt(sum) + parseInt($(inputs[i]).val());
		 $("#sum").html("Rs. "+sum);
	}
});

</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("blur", ".sal", function () {
    var $row = $(this).closest(".qt");
	var row = $(this).parent().parent();
	var pr = row.find('.serr').val();
	var qt = row.find('.qt').val();
	var bol = checkstock(pr);
	
	//alert(bol);
	if(bol=="2"){
		toastr.warning("Duplicate Entry");
		row.find(".serr").val("");
		row.find(".ser").val("");
		row.find(".qt").val("0");
		row.find(".prr").val("");
		row.find(".pr").val("");
	}
});
});
</script>
	
<script>
function checkstock(sid) {
	var so = 0;
	var ids = $(".serr");
	var qt = $(".qt");
	var inputs = $(".price");
	for(var i = 0; i < inputs.length; i++){
		//var service = $(ids[i]).val().split(',');
		if($(ids[i]).val()==sid){
			so++;
			//alert(so);
		}
	}
return so;	
}
</script>
		
<?php 
//include "footer.php";

function setclient($cl,$cont,$dob,$gender){
		global $con;
	$notes = mysqli_real_escape_string($con, $_POST['notes']);
	mysqli_query($con,"INSERT INTO `client`(`name`,`cont`,`gender`,`dob`,`active`) VALUES ('$cl','$cont','$gender','$dob',0)") or die(mysqli_error($con));
	
	$aid = mysqli_insert_id($con);
	return $aid;
	}
	
function getclient($cid){
		global $con;
		$sql="SELECT * from client where id=$cid";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['name'];
		}
	}
	function getcont($cid){
		global $con;
		$sql="SELECT * from client where id=$cid";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['cont'];
		}
	}
	
	function getbeautician($id){
		global $con;
		$sql="SELECT * FROM `beauticians` where id=$id and active=0";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['cont'];
		}
	}
	
	
	function getservice($id){
		global $con;
		$sql="SELECT * FROM `beauticians` where id=$id and active=0";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['cont'];
		}else{
			return 0;
		}
	}
	
	function getduration($cid){
		global $con;
		$sql="SELECT * FROM `service` where id=$cid";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['duration'];
		}
	}
	
 ?>