<?php 
	include_once './includes/db_include.php';
	if(!isset($_SESSION['branch_id'])){
	    $branch_id = decrypt_url($_GET['invshopid']);
	    if($branch_id == '' || $branch_id <= 0){
	        header('LOCATION: index.php');
	    }
	} else {
	    $branch_id = $_SESSION['branch_id'];
	}
	
	if(isset($_GET['inv'])){
		$inv = $_GET['inv'];
		$edit = query_by_id("SELECT i.itime, i.due,i.paid,i.bpaid,i.advance,i.taxtype,i.tax,cou.discount,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.gst,i.coupon as coupon_id,i.dis,i.doa,i.id,i.total,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.address,c.email, md.id as membership_id, md.membership_name from `invoice_".$branch_id."` i"
		." LEFT JOIN `client` c on c.id=i.client" 
		." LEFT JOIN `coupons` cou on cou.id=i.coupon"
		." LEFT JOIN `membership_discount` md ON md.id = i.membership_id"
		." where i.active=0  and i.id='$inv' and i.branch_id='".$branch_id."' order by i.id desc",[],$conn)[0];
	}
	if(isset($_GET['invMencr'])){
		$inv = decrypt_url($_GET['invMencr']);
		$edit = query_by_id("SELECT i.itime, i.due,i.paid,i.bpaid,i.advance,i.taxtype,i.tax,cou.discount,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.gst,i.coupon as coupon_id,i.dis,i.doa,i.id,i.total,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.address,c.email, md.id as membership_id, md.membership_name from `invoice_".$branch_id."` i"
		." LEFT JOIN `client` c on c.id=i.client" 
		." LEFT JOIN `coupons` cou on cou.id=i.coupon "
		." LEFT JOIN `membership_discount` md ON md.id = i.membership_id "
		." where i.active=0  and i.id='$inv' and i.branch_id='".$branch_id."' order by i.id desc",[],$conn)[0];
	}

	
?>

<!DOCTYPE html>
<html lang="en" class="gr__s_bootsnipp_com"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta name="robots" content="noindex">
	
    <title>Invoice</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="invoice/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style type="text/css">
		.invoice-title h2, .invoice-title h3 {
			display: inline-block;
		}
		
		.table > tbody > tr > .no-line {
			border-top: none;
		}
		
		.table > thead > tr > .no-line {
			border-bottom: none;
		}
		
		.table > tbody > tr > .thick-line {
			border-top: 2px solid;
		}
		body{
		    font-family: 'Roboto', sans-serif!important;
		}
		
		@media print {
    		.printbtn {
    		    display: none;
    		}
    		body{
		        font-family: 'Roboto', sans-serif!important;
		    }
		}
	</style>
    <script src="../salonSoftFiles_new/js/jquery.js"></script>
	<link href="../salonSoftFiles_new/css/bootstrap.min.css" media="screen" rel="stylesheet" />
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/js/toastr.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/css/toastr.css">
    
</head>
<body data-gr-c-s-loaded="true" cz-shortcut-listen="true">
    
    <!-- Company profile -->
    
    <?php for($i = 1; $i <= 2 ; $i++){ ?>
    
    <div style="text-align:center;font-size:40px">
        <p style="float:left;margin:0px;"><img src="<?php echo systemlogo($conn); ?>" alt="<?php echo systemname($conn); ?>" style="max-width:150px"/></p>
        <div style="float:right">
		<?php 
			$sql1="SELECT * FROM `system` where branch_id='".$branch_id."'";
			$result1=query_by_id($sql1,[],$conn);
			foreach($result1 as $row1) { ?>
			<p style="font-size:12px;text-align:right;margin:0px auto;text-transform:uppercase;margin-bottom:2px;"><b><?= $row1['address']; ?></b></p>
			<p style="font-size:12px;text-align:right;margin:0px auto;"><b>Contact : <?= $row1['phone']; ?></b></p>
			<?php if($row1['email']!=''){ ?>
				<p style="font-size:12px;text-align:right;margin:0px auto;"><b>Email : <?= $row1['email']; ?></b></p>
			<?php } ?>
			<?php if($row1['website']!=''){ ?>
				<p style="font-size:12px;text-align:right;margin:0px auto;"><b>Website : <?= $row1['website']; ?></b></p>
			<?php } ?>
			<?php if($row1['gst']!=''){ ?>
				<p style="font-size:12px;text-align:right;margin:0px auto;"><b>GST No : <?= $row1['gst']; ?></b></p>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
	<div style="clear:both;width:100%;"></div>
	<h4 style="letter-spacing:4px;text-align:center;">SALES INVOICE</h4>
	<p style="font-size:14px;max-width:250px;text-align:center;margin:0px auto;"><b>(Branch : </b><?= ucfirst(branch_by_id($branch_id)); ?>)</p>
	<hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
	
	<!-- Customer Info -->
	
	<div style="padding:0px 12px;">
        <table style="width:100%;font-size:12px;font-weight:600;">
            <?php if($edit['name']) { ?>
            <tr style="vertical-align: top;">
                <td>Customer Name</td>
                <td width="200"> : <?= $edit['name'] ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['cont']) { ?>
            <tr style="vertical-align: top;">
                <td>Mobile No</td>
                <td> : <?= $edit['cont'] ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['email']) { ?>
            <tr style="vertical-align: top;">
                <td>Email Id </td>
                <td> : <?= $edit['email'] ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['address']) { ?>
            <tr style="vertical-align: top;">
                <td>Address </td>
                <td> : <?= $edit['address'] ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['gst']) { ?>
            <tr style="vertical-align: top;">
                <td>GST No </td>
                <td> : <?= $edit['gst'] ?></td>
            </tr>
            <?php } ?>
            <?php if(number_format(client_wallet_uptodate($edit['c_id'],$edit['doa'],date("H-i",strtotime("+10 minutes", strtotime($edit['itime']))))) > 0) { ?>
            	<tr style="vertical-align: top;">
	                <td>Wallet Balance:</td>
	                <td> : <?= CURRENCY ." ".  number_format(client_wallet_uptodate($edit['c_id'],$edit['doa'],date("H-i",strtotime("+10 minutes", strtotime($edit['itime']))))) ?> /-</td>
            	</tr>
            <?php } ?>
            <?php if($edit['id']) { ?>
            <tr style="vertical-align: top;">
                <td>Invoice No </td>
                <td> : #INV<?= sprintf('%04d',$edit['id']); ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['membership_id']) { ?>
            <tr style="vertical-align: top;">
                <td>Membership id</td>
                <td> : #MEM<?= sprintf('%04d',$edit['membership_id']); ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['membership_name']) { ?>
            <tr style="vertical-align: top;">
                <td>Membership name</td>
                <td> : <?= $edit['membership_name']; ?></td>
            </tr>
            <?php } ?>
            <?php if($edit['doa']) { ?>
            <tr style="vertical-align: top;">
                <td>Invoice Date </td>
                <td> : <?= my_date_format($edit['doa']); ?> <?= my_time_format($edit['itime']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <!-- Order summary -->

    <hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
	<div>
		<table style="width:100%;font-size:12px;font-weight:600;border-collapse:collapse;">
			<thead>
				<tr style="font-size: 11px;">
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Service &amp; Product</strong></td>
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Provider</strong></td>
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Rate</strong></td>
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Dis</strong></td>
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Qty</strong></td>
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;text-align:right;"><strong>Total</strong></td>
				</tr>
			</thead>
			<tbody>
			    <?php
    			$sql2="SELECT ii.id as ii_iddd,imsp.ii_id,imsp.service_Provider,GROUP_CONCAT(b.name,' ') as name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,ii.actual_price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,scat.cat as cat_name from `invoice_items_".$branch_id."` ii"
						." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
						." LEFT JOIN `servicecat` scat on scat.id=s.cat"
						." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
						." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
						." where ii.iid='$inv' and ii.branch_id='".$branch_id."' and imsp.branch_id='".$branch_id."' and ii.active=0 GROUP BY ii_id";
    				$result2=query_by_id($sql2,[],$conn);
    				$count = 1;
    				$sp_id = 0;
    				$total_qty = 0;
    				foreach($result2 as $row1) {
    				?>
    				<tr style="font-size:12px;">
    					<td style="padding:2px 7px;vertical-align:top;"><?=getEnquiryfor($row1['service']); ?> <?php
    					$ispackage_ser = query_by_id("SELECT count(*) as total,p.name as pck_name FROM package_service_history psh LEFT JOIN `invoice_items_".$branch_id."` ii ON psh.invoice_id = ii.iid LEFT JOIN packages p ON psh.package_id = p.id WHERE ii.actual_price = 0 AND psh.status='1' AND ii.id = '".$row1['ii_iddd']."' AND ii.iid ='". $edit['id']."'",[],$conn)[0];
						if($ispackage_ser['total'] != 0){
    						echo "(PKG. DED-".$ispackage_ser['pck_name'].")";	
    					}
    					?></td>
    					<td style="padding:2px 7px;vertical-align:top;">
    					    <?php
    					        $sprovider = explode(',',$result2[$sp_id]['name']);
    					        $slast = end($sprovider);
    					        foreach($sprovider as $sp){
    					            if(count($sprovider) > 1 && $sp != $slast){
    					                $cat = ',';
    					            } else { $cat = ''; }
    					            echo ucfirst(strtolower($sp)).$cat."<br />";
    					        }
    					    ?>
    					</td>
    					<td style="padding:2px 7px;vertical-align:top;"><?= $row1['actual_price'] ?></td>
    					<td style="padding:2px 7px;vertical-align:top;"><?=(EXPLODE(",",$row1['disc_row'])[0] == 'pr')?EXPLODE(",",$row1['disc_row'])[1].' %':EXPLODE(",",$row1['disc_row'])[1] ?></td>
    					<td style="padding:2px 7px;vertical-align:top;"><?= $row1['quantity']; ?></td>
    					<td style="padding:2px 7px;vertical-align:top;text-align:right;"><?= $row1['price'] ?></td>
    				</tr>
			<?php $count++; $sp_id++; $total_qty += $row1['quantity']; } ?>
		    </tbody>
		</table>
		<hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
		<table style="width:100%;font-size:12px;font-weight:600;border-collapse:collapse;">
		    <tr>
		        <td style="padding:0px 7px;"><strong>Total Qty </strong></td>
		        <td style="padding:0px 7px;"> : <?= $total_qty ?></td>
		        <td style="text-align:right;padding:0px 7px;">Total :</td>
		        <td style="text-align:right;padding:0px 7px;"><?= number_format($edit['subtotal'],2); ?></td>
		    </tr>
		    <tr>
		        <td rowspan="10" colspan="2" style="vertical-align:top;padding:0px 7px;">Payment Mode : <br />
		            <?php $paystatus = '';
                    	$paymethod = "SELECT pm.name as name, mpm.transaction_id FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='bill,".$edit['id']."' and mpm.branch_id='".$branch_id."'";
                    	$methodres = query_by_id($paymethod,[],$conn);
                    	foreach ($methodres as $res) {
                    		$paystatus .= ucfirst($res['name']);
                    		if($res['transaction_id'] != ''){
                    		    $paystatus .= ": ".$res['transaction_id']."<br />";
                    		} else {
                    		    $paystatus .= "<br />";
                    		}
                    	} echo $paystatus; 
                    ?>
		        </td>
		        <td style="text-align:right;padding:0px 7px;">Coupon Dis :</td>
		        <td style="text-align:right;padding:0px 7px;"><?php
		            $coupon_dis = $edit['discount']?$edit['discount']:'0 ';
		            $discount_type = $edit['discount_type'] == '0'?'%':'';
		            echo $coupon_dis.$discount_type;
		        ?></td>
		    </tr>
		    <tr>
		        <td style="text-align:right;padding:0px 7px;">Discount : </td>
		        <td style="text-align:right;padding:0px 7px;"><?= number_format(EXPLODE(',',$edit['dis'])[1],2)." ".((EXPLODE(',',$edit['dis'])[0] == 'pr')?'%':'') ?></td>
		    </tr>
		    <?php    
				$tax_val = gettaxx($edit['tax']);
				if($tax_val>0){
			?>
		    <tr>
		        <td style="text-align:right;padding:0px 7px;">Tax Type : </td>
		        <td style="text-align:right;padding:0px 7px;"><?php 
						$discount = EXPLODE(',',$edit['dis'])[1];
						if($edit['taxtype'] == '0')
						echo " Inclusive";
						else if($edit['taxtype'] == '1')
						echo "Exclusive";
					?></td>
		    </tr>
		    <tr>
		        <td style="text-align:right;padding:0px 7px;"></td>
		        <td style="text-align:right;padding:0px 7px;"><?php 
					$sub=$edit['subtotal'] * $coupon_dis / 100;
					if($discount != 0){
					    if(EXPLODE(',',$edit['dis'])[0] == CURRENCY){
					        $value_after_discount = ($edit['subtotal'] - $sub)-$discount;
					    } else {
					        $value_after_discount = $edit['subtotal']-($edit['subtotal'] - $sub) * $discount / 100;
					    }
					}else{
					    $value_after_discount = ($edit['subtotal'] - $sub);
					}
				?></td>
		    </tr>
		    <tr>
				<td style="text-align:right;padding:0px 7px;"><strong>SGST(<?= gettaxx($edit['tax'])/2 ?>%) : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?php echo number_format(getsum($edit['tax'],$value_after_discount,$edit['taxtype'])/2,2); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>CGST(<?= gettaxx($edit['tax'])/2 ?>%) : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?php echo number_format(getsum($edit['tax'],$value_after_discount,$edit['taxtype'])/2,2); ?></td>
			</tr>
			<?php
				}else{
			?>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Tax : </strong></td>
				<td style="text-align:right;padding:0px 7px;">0</td>
			</tr>
			<?php } ?>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Total : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?= number_format($edit['total'],2); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Advance : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?= number_format($edit['advance'],2); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Amount Paid : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?= number_format($edit['paid'],2); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Amount Due : </strong></td>
				<td style="text-align:right;padding:0px 7px;"><?= number_format($edit['due'],2); ?></td>
			</tr>
		</table>
		<hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
		<!--<h4 style="font-size:13px;font-weight:600;"><u>Terms &amp; Condition:</u></h4>-->
		<!--<p style="font-size:14px;font-weight:600;">1. Discounts are not applicable on Products. MRP mentioned are inclusive of taxes<br >-->
		<!--2. We are open 7 Days from 10:30 A.M TO 8:00 P.M <br />-->
		<!--3. For Appointments call at 9836402666 or 033-24546540 for Elgin road Branch <br />-->
		<!--4. Products once sold are not returnable. <br />-->
		<!--5. Follow us on facebook page or visit www.trendzsalon.co.in-->
		<!--</p>-->
		<?php if($i == 1){ ?>
		<br />
		<div style="border: 1px dashed #000; width: 100%;"></div>
		<br />
		<?php } } ?>
		
		<p style="text-align:center;font-size:12px;font-weight:600;">****THANK YOU. PLEASE VISIT AGAIN****</p>
	</div>
	<div><center><button onclick="window.print();" class="printbtn btn btn-info">Print</button>
	<?php if(isset($_GET['inv'])){ ?>
		<a href="billing.php" class="printbtn"><button class="printbtn btn btn-info">Back</button></a></center><br></div>
	<?php } ?>
	
	<script>
		function myalert() {
			<?php 
				$t = $_SESSION['t'];
				if($t==1){
					echo 'toastr.success("'.$_SESSION['tmsg'].'");';
					}else if($t==2){
					echo 'toastr.error("'.$_SESSION['tmsg'].'", "Error");';
				}
			?>
			
			<?php $_SESSION['t'] = 0;
				$_SESSION['tmsg'] = 0;
			?>
		}
		
		$(function(){
			window.localStorage.setItem('refresh','1');
		});
	</script>
	
	
</body></html>
<?php 
	
 
	 
	
	function getprice($id,$type){
		global $conn;
		global $branch_id;
		$str = "";
		switch ($type) {
			case "Service":
			$sql="SELECT * from `service` where active='0' and id=$id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['price'];
			}
			break;
			case "Product":
			$sql="SELECT * from `products` where active='0' and id=$id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['price'];
			}
			break;
			case "Package":
			$sql="SELECT * from `packages` where active='0' and id=$id and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['price'];
			}
			break;
			default:
			$str = "";
			break;
		}
		return $str;
	}
	
	function gettax($tid){
		global $conn;
		$sql="SELECT * from `tax` where active='0' and id=$tid";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['title']."( ".$row['tax']."% )";
			}
		}
	
	
	function gettaxx($tid){
		global $conn;
		$sql="SELECT * from `tax` where active='0' and id=$tid";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['tax'];
			}
		}
	
	function getsum($tid,$val,$type){
	 
		if($type==1){
			$tax = gettaxx($tid);
			$sum = $val * $tax / 100;
		}else{
			$tax = gettaxx($tid);
			$sum = $val - (($val / (100+$tax)) * 100);
		}
		return $sum;
	}
	
?>