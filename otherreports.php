<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";
?>
<style>
    @media print {
        #fetch_balace_report{
            display:block;
        }
        header, nav, footer, .heading-with-btn, .col-lg-3.col-md-4.col-sm-4.col-xs-12, #date_filter, #paymentModal {
            display:none;
        }
    }
</style>
<!--<div id="displayAjaxContent"> </div>-->
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<div class="row gutter">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">

					<div class="panel-heading">
						<h4 id='payment_mode' class="pull-left">Sales Report</h4>
					    <div class="clearfix"></div>
					</div>
					<div class="panel-body">					
						<div class="row">
                            <br/>
							<div class="col-lg-12">
								
								<div id="fetch_sales_report">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<!-- Customer Modal -->
<div id="customer_type" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title cust_modal_title"></h4>
      </div>
      <div class="modal-body cust_modal_body">
      </div>
      <!--<div class="modal-footer">-->
      <!--  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
      <!--</div>-->
    </div>

  </div>
</div>

 <script>
 	 function fetch_data(method_id){
 		 var text = " Sales Report";
 		 document.getElementById("payment_mode").innerHTML = text;
 		 $.ajax({
    		 url:"ajax/fetch_sales_report.php",
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_sales_report').html(data);
				}
			 },
		 });
 	 }
		
	$(function(){
	var setTimer
	var StartDispalyingTimer = function (){var start = 1;
        setTimer = setInterval(function () {start++;}, 1000);
    }

	$('#date_filter').click(function(){
        var daterange = $('#daterange').val();
        var pageinfo = $('#modes').val();
    	var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}

    	 $.ajax({
    		 url:"ajax/fetch_sales_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo="+pageinfo,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
		 });
	 });
	 $.ytLoad();
    
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)
    
	function isoDate(date){	
	    var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
		 
	});
	
	$('document').ready(function(){
		fetch_data(1);
	});

    function cust(type){
     $.ajax({
         url:"ajax/fetch_cust.php?type="+type,
         method:"GET",
         success:function(data){
             if(data){
                if(type==0)
                    $(".cust_modal_title").html("New Customers");
                else
                    $(".cust_modal_title").html("Repeated Customers");
                 $(".cust_modal_body").html(data);
                 $("#customer_type").modal("show");
             }
         }
     }); 
    }
    

    

 </script>
 <?php  include "footer.php";?>