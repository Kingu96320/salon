<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	$type = "";
	$user = "";
	
	if(isset($_POST['submit']))
	{
		$type = $_POST['type'];
		if(strlen($type)>0)
		$type = " and type='$type'";
		
		$user = $_POST['user'];
		if(strlen($user)>0)
		$user = " and leaduser='$user'";
		
		$date = $_POST['date'];
		if(strlen($date)>0)
		$date = " and regon='$date'";
		
		$status = $_POST['status'];
		if(strlen($status)>0)
		$status = " and leadstatus='$status'";
		
		$submit = " ".$type." ".$user." ".$date." ".$status;
	}
	if(isset($_POST['reset'])){
		$type = "";
		$user = "";
		$date = "";
	}
	
	if(isset($_POST['rsubmit'])){
		$sdate = $_POST['fdate'];
		$edate = $_POST['tdate'];
		
		if(strlen($sdate)>0||strlen($edate)>0)
		$submit = "and DATE(regon) BETWEEN $sdate AND $edate";
	}
	
	$sql="SELECT * from enquiry where active=0 and branch_id='".$branch_id."' $submit order by id desc";
	include "topbar.php";
	include "header.php";
	include "menu.php";
	
	$date = date('Y-m-d');
?>
<style>
    .nav-tabs li a:hover{
        margin-bottom:1px;
    }
</style>

<div class="dashboard-wrapper">
    <div class="main-container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#available_stock">Available stock</a></li>
            <li><a data-toggle="tab" href="#expired_stock">Expired stock</a></li>
            <!-- <li><a data-toggle="tab" href="#returned_stock">Returned items</a></li> -->
        </ul>
        <div class="tab-content">
            <!-- Available stock start -->
            <div id="available_stock" class="tab-pane fade in active">
                <div class="row gutter">
        			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        				<form action="" method="post">
        					<div class="panel">
							<div class="panel-heading heading-with-btn">
								<h4 class="pull-left">Available Stock</h4>
								<span id="download-btn1"></span>					
								<div class="clearfix"></div>
							</div>
        						<div class="panel-body">
            						<div class="table-responsive">
            							<table class="table table-bordered no-margin" id="available_stock_tbl">
            								<thead>
            									<tr>
            									    <th>Lot no</th>
            										<th>Product name</th>
            										<th>Available in stock</th>
            										<th>Sale price</th>
            										<th>Expiry date</th>
            										<th>Action</th>
            									</tr>
            								</thead>
            								<tbody>
            									<?php
            										$sql1="SELECT ps.*, ps.id as item_id, ps.quantity as qty, p.name, p.id as pid, p.volume, u.name as unit from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id LEFT JOIN units u ON u.id = p.unit where p.active=0 and ps.active=0 and ps.exp_date >='".$date."' and ps.branch_id='".$branch_id."' order by ps.iid DESC";
            										$result1=query_by_id($sql1,[],$conn);
            										//foreach($result1 as $row1) {
            										$uniquename = array_unique($result1);
                                                $uniqueproduct = array();
                                                foreach ($result1 as $uniquedata) {
                                                    if (!in_array($uniquedata['product_id'],$uniqueproduct)) {
                                                        array_push($uniqueproduct, $uniquedata['product_id']);
                                                    }
                                                }
                                                 
                                                $totalarray = array();
                                                foreach ($uniqueproduct as $uniqueprod) {
                                                    $totalqty = 0;
                                                       
                                                    foreach ($result1 as $row123) {
                                                        if ($row123['product_id'] == $uniqueprod) {
                                                            $totalqty = $totalqty + $row123['qty'];
                                                        }
                                                        }
                                                        
                                                  
                                                    $d['prodid'] = $uniqueprod;
                                                    $d['totamt'] = $totalqty;
                                                    array_push($totalarray, $d);
                                                }
                                                
                                                
                                                foreach($totalarray as $totqty){ 
                                                    $row1 =array();
                                                foreach ($result1 as $rowinfo) {
                                                    if($rowinfo['product_id'] == $totqty['prodid']){
                                                        $row1 = $rowinfo;
                                                    }
                                                }
                                                        
                                                
            										
            										?>
            										<tr>
            										    <td><?php echo $row1['iid'] ?></td>
            											<td><?php 
            											    if($row1['exp_date'] < date('Y-m-d')){
            											        echo "<span class='text-danger'>(Expired) <del>".$row1['name'].' ('.$row1['volume'].' '.$row1['unit'].')'."</del></span>";
            											    } else {
            											        echo $row1['name'].' ('.$row1['volume'].' '.$row1['unit'].')';
            											    }
            											?></td>
            											<td><?php  echo $totqty['totamt'];
            											//echo getstock($row1['pid'],0,$row1['item_id']); 
            											?></td>
            											<td><?php echo number_format($row1['sale_price'],2); ?></td>
            											<td><?php echo date('d-M-Y',strtotime($row1['exp_date'])); ?></td>
            											<td>
            											    <a href="productdetail.php?pid=<?php echo $row1['pid']; ?>&item_id=<?php echo $row1['item_id'] ?>" target="_blank">
            											        <button class="btn btn-warning btn-xs"  type="button"><i class="fa fa-history" aria-hidden="true"></i>View history</button>
            											    </a>
            											</td>
            										</tr>
            										<?php } ?>
            								</tbody>
            							</table>
            						</div>
            					</div>
        					</div>
        				</form>
        			</div>
        		</div>
            </div>
            <!-- Available stock end -->
            
            <!-- Expired stock start -->
            <div id="expired_stock" class="tab-pane fade">
                <div class="row gutter">
            		<div class="col-lg-12">	
            			<div class="panel">
            				<div class="panel-heading heading-with-btn">
            					<h4 class="pull-left">Expired stock</h4>
								<span id="download-btn2"></span>					
								<div class="clearfix"></div>
            				</div>
            				<div class="panel-body">
        						<div class="table-responsive">
        							<table class="table table-bordered no-margin" id="expired_stock_tbl">
        								<thead>
        									<tr>
        									    <th>Lot no</th>
        										<th>Product name</th>
        										<th>Pending in stock</th>
        										<th>Purchase price</th>
        										<th>Sale price</th>
        										<th>Expiry date</th>
        										<th>Action</th>
        									</tr>
        								</thead>
        								<tbody>
        									<?php
        										$sql1="SELECT ps.*, ps.id as item_id, p.name, p.id as pid, p.volume, u.name as unit from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id LEFT JOIN units u ON u.id = p.unit where p.active=0 and ps.active=0 and ps.exp_date < '".$date."' and ps.branch_id='".$branch_id."' order by ps.iid DESC";
        										$result1=query_by_id($sql1,[],$conn);
        										foreach($result1 as $row1) {
        										?>
        										<tr>
        										    <td><?php echo $row1['iid'] ?></td>
        											<td><?php 
        											    if($row1['exp_date'] < date('Y-m-d')){
        											        echo "<span class='text-danger'>(Expired) <del>".$row1['name'].' ('.$row1['volume'].' '.$row1['unit'].')'."</del></span>";
        											    } else {
        											        echo $row1['name'].' ('.$row1['volume'].' '.$row1['unit'].')';
        											    }
        											?></td>
        											<td><?php echo getstock($row1['pid'],0,$row1['item_id']); ?></td>
        											<td><?php echo number_format($row1['price'],2); ?></td>
        											<td><?php echo number_format($row1['sale_price'],2); ?></td>
        											<td><?php echo date('d-M-Y',strtotime($row1['exp_date'])); ?></td>
        											<td>
        											    <a href="productdetail.php?pid=<?php echo $row1['pid']; ?>&item_id=<?php echo $row1['item_id'] ?>" target="_blank">
        											        <button class="btn btn-warning btn-xs"  type="button"><i class="fa fa-history" aria-hidden="true"></i>View history</button>
        											    </a>
        											    <!-- <a href="javascript:void(0)">
        											        <button class="btn btn-xs btn-danger"><i class="fa fa-share" aria-hidden="true"></i>Return</button>
        											    </a> -->
        											</td>
        										</tr>
        										<?php } ?>
        								</tbody>
        							</table>
        						</div>
        					</div>
            			</div>
            		</div>
            	</div>
            </div>
            <!-- Expired stock end -->
            
            <!-- Returned stock to vendor start -->
            <!--<div id="returned_stock" class="tab-pane fade">
                <div class="row gutter">
            		<div class="col-lg-12">	
            			<div class="panel">
            				<div class="panel-heading heading-with-btn">
            					<h4 class="pull-left">Returned items</h4>
            					<span id="download-btn3" ></span>
            					<div class="clearfix"></div>
            				</div>
            				<div class="panel-body">
        						<div class="table-responsive">
        							<table class="table table-bordered no-margin table_datatable" id="returned_stock_tbl">
        								<thead>
        									<tr>
        									    <th>Lot no</th>
        										<th>Product name</th>
        										<th>Quantity</th>
        										<th>Purchase price</th>
        										<th>Expired on</th>
        										<th>Returned on</th>
        										<th>Action</th>
        									</tr>
        								</thead>
        								<tbody>
        									
        								</tbody>
        							</table>
        						</div>
        					</div>
            			</div>
            		</div>
            	</div>
            </div>-->
            <!-- Returned stock to vendor end -->
        </div>
        
    </div>
</div>
</div>
<script>
    $(document).ready(function(){

    		var table1 = $('#available_stock_tbl').DataTable( {
			dom: 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			// {
			// 	extend: 'csvHtml5',
			// 	text: '<i class="fa fa-file-text-o"></i> CSV',
			// 	titleAttr: 'CSV',
			// 	title: '<?php //echo systemname($conn); ?>',
			// 	exportOptions: {
			// 		columns: ':not(:last-child)',
			// 	}
			// },
			// {
			// 	extend: 'print',
			// 	exportOptions: {
			// 		columns: ':visible'
			// 	},
			// 	customize: function(win) {
			// 		$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
			// 	}
			// },
			],
			
		} );

		// To move download button in another div out of the dataTables
		var buttons = new $.fn.dataTable.Buttons(table1, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child):not(.not-export-column)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn1'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
		
    

    		var table2 = $('#expired_stock_tbl').DataTable( {
			dom: 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			// {
			// 	extend: 'csvHtml5',
			// 	text: '<i class="fa fa-file-text-o"></i> CSV',
			// 	titleAttr: 'CSV',
			// 	title: '<?php //echo systemname($conn); ?>',
			// 	exportOptions: {
			// 		columns: ':not(:last-child)',
			// 	}
			// },
			// {
			// 	extend: 'print',
			// 	exportOptions: {
			// 		columns: ':visible'
			// 	},
			// 	customize: function(win) {
			// 		$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
			// 	}
			// },
			],
			
		} );

		// To move download button in another div out of the dataTables
		var buttons = new $.fn.dataTable.Buttons(table2, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child):not(.not-export-column)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn2'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
		
        

    		var table3 = $('#returned_stock_tbl').DataTable( {
			dom: 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			// {
			// 	extend: 'csvHtml5',
			// 	text: '<i class="fa fa-file-text-o"></i> CSV',
			// 	titleAttr: 'CSV',
			// 	title: '<?php //echo systemname($conn); ?>',
			// 	exportOptions: {
			// 		columns: ':not(:last-child)',
			// 	}
			// },
			// {
			// 	extend: 'print',
			// 	exportOptions: {
			// 		columns: ':visible'
			// 	},
			// 	customize: function(win) {
			// 		$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
			// 	}
			// },
			],
			
		} );

		// To move download button in another div out of the dataTables
		var buttons = new $.fn.dataTable.Buttons(table3, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child):not(.not-export-column)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn3'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
		
    });
</script>

<?php  include "footer.php"; ?>