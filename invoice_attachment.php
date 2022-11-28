<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$inv = '41';
	$edit = query_by_id("SELECT i.itime, i.due,i.paid,i.bpaid,i.advance,i.taxtype,i.tax,cou.discount,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.gst,i.coupon as coupon_id,i.dis,i.doa,i.id,i.total,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.address,c.email, md.id as membership_id, md.membership_name from `invoice_".$branch_id."` i"
	." LEFT JOIN `client` c on c.id=i.client" 
	." LEFT JOIN `coupons` cou on cou.id=i.coupon"
	." LEFT JOIN `membership_discount` md ON md.id = i.membership_id"
	." where i.active=0  and i.id='$inv' and i.branch_id='".$branch_id."' order by i.id desc",[],$conn)[0];

	$html = '';
	$html .= '<!DOCTYPE html>
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
			    font-family: "Roboto", sans-serif!important;
			}
			
			@media print {
	    		.printbtn {
	    		    display: none;
	    		}
	    		body{
			        font-family: "Roboto", sans-serif!important;
			    }
			}
		</style>
	    <script src="../salonSoftFiles_new/js/jquery.js"></script>
		<link href="../salonSoftFiles_new/css/bootstrap.min.css" media="screen" rel="stylesheet" />
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/js/toastr.js"></script>
	    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/css/toastr.css">
	    
	</head>
	<body data-gr-c-s-loaded="true" cz-shortcut-listen="true" style="margin-top:30px; margin-bottom:30px;">
	    <div style="width: 600px;background: #fff;margin: 0px auto;border:3px solid #000; position: relative;padding:15px;">
	    <!-- Company profile -->
	    <h4 style="text-align:left;font-weight:600;">SALES INVOICE 	<span style="font-size:14px;float:right;">Branch : '.ucfirst(branch_by_id($branch_id)).'</span></h4>
	    <div  style="font-size:40px">
	        <div style="float:left;width:200px;"><img src="'.systemlogo($conn).'" alt="'.systemname($conn).'" style="max-width:120px"/></div>
	        <div style="float:left;float:right;">';
			$sql1="SELECT * FROM `system` where branch_id='".$branch_id."'";
			$result1=query_by_id($sql1,[],$conn);
			foreach($result1 as $row1) { 
			$html .= '<p style="font-size:12px;max-width:250px;margin:0px auto;text-transform:uppercase;margin-bottom:2px;"><b>'.$row1['address'].'</b></p>
			<p style="font-size:12px;max-width:250px;margin:0px auto;"><b>Contact : '.$row1['phone'].'</b></p>';
			if($row1['email']!=''){
				$html .= '<p style="font-size:12px;max-width:250px;margin:0px auto;"><b>Email : '.$row1['email'].'</b></p>';
			}
			if($row1['website']!=''){
				$html .= '<p style="font-size:12px;max-width:250px;margin:0px auto;"><b>Website : '.$row1['website'].'</b></p>';
			}
			if($row1['gst']!=''){
				$html .= '<p style="font-size:12px;max-width:250px;margin:0px auto;"><b>GST No : '.$row1['gst'].'</b></p>';
			}
		}
	$html .= '
	</div></div><div class="clearfix"></div>
	<hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
	
	<div style="">
        <table style="width:100%;font-size:12px;font-weight:600;">';
            if($edit['name']) {
            $html .= '<tr style="vertical-align: top;">
                <td width="340">Customer Name</td>
                <td> : '.$edit['name'].'</td>
            </tr>';
            }
            if($edit['cont']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Mobile No</td>
                <td> : '.$edit['cont'].'</td>
            </tr>';
            }
            if($edit['email']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Email Id </td>
                <td> : '.$edit['email'].'</td>
            </tr>';
            }
            if($edit['address']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Address </td>
                <td> : '.$edit['address'].'</td>
            </tr>';
            }
            if($edit['gst']) {
            $html .= '<tr style="vertical-align: top;">
                <td>GST No </td>
                <td> : '.$edit['gst'].'</td>
            </tr>';
            }
            if($edit['id']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Invoice No </td>
                <td> : #INV'.sprintf('%04d',$edit['id']).'</td>
            </tr>';
            }
            if($edit['membership_id']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Membership id</td>
                <td> : #MEM'.sprintf('%04d',$edit['membership_id']).'</td>
            </tr>';
            }
            if($edit['membership_name']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Membership name</td>
                <td> : '.$edit['membership_name'].'</td>
            </tr>';
            }
            if($edit['doa']) {
            $html .= '<tr style="vertical-align: top;">
                <td>Invoice Date </td>
                <td> : '.my_date_format($edit['doa']).' '.my_time_format($edit['itime']).'</td>
            </tr>';
            }
        $html .= '</table>
    </div>
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
					<td style="padding:0px 7px 12px 7px;border-bottom:1px solid #8c8c8c;"><strong>Total</strong></td>
				</tr>
			</thead>
			<tbody>';
    			$sql2="SELECT imsp.ii_id,imsp.service_Provider,GROUP_CONCAT(b.name,' ') as name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,ii.actual_price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,scat.cat as cat_name from `invoice_items_".$branch_id."` ii"
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
    					$sp_name = '';    				
						$sprovider = explode(',',$result2[$sp_id]['name']);
				        $slast = end($sprovider);
				        foreach($sprovider as $sp){
				            if(count($sprovider) > 1 && $sp != $slast){
				                $cat = ',';
				            } else { $cat = ''; }
				            $sp_name .= ucfirst(strtolower($sp)).$cat.'<br />';
				        }

				        $dis = (EXPLODE(",",$row1['disc_row'])[0] == 'pr')?EXPLODE(",",$row1['disc_row'])[1].' %':EXPLODE(",",$row1['disc_row'])[1];
	    				$html .= '<tr style="font-size:12px;">
	    					<td style="padding:5px 7px;vertical-align:top;">'.getEnquiryfor($row1['service']).'</td>
	    					<td style="padding:5px 7px;vertical-align:top;">'.$sp_name.'</td>
	    					<td style="padding:5px 7px;vertical-align:top;">'.$row1['actual_price'].'</td>
	    					<td style="padding:5px 7px;vertical-align:top;">'.$dis.'</td>
	    					<td style="padding:5px 7px;vertical-align:top;">'.$row1['quantity'].'</td>
	    					<td style="padding:5px 7px;vertical-align:top;">'.$row1['price'].'</td></tr>';
					$count++; $sp_id++; $total_qty += $row1['quantity']; 
				}
			$html .= '</tbody>
		</table>
		<hr style="margin-top:9px;margin-bottom:9px;border-color:#8c8c8c;">
		<table style="width:100%;font-size:12px;font-weight:600;border-collapse:collapse;">
		    <tr>
		        <td style="padding:0px 7px;"><strong>Total Qty </strong></td>
		        <td style="padding:0px 7px;"> : '.$total_qty.'</td>
		        <td style="text-align:right;padding:0px 7px;">Total :</td>
		        <td style="text-align:right;padding:0px 7px;">'.number_format($edit['subtotal'],2).'</td>
		    </tr>
		    <tr>
		        <td rowspan="10" colspan="2" style="vertical-align:top;padding:0px 7px;">Payment Mode : <br />';
		            $paystatus = '';
                    	$paymethod = "SELECT pm.name as name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='bill,".$edit['id']."' and mpm.branch_id='".$branch_id."'";
                    	$methodres = query_by_id($paymethod,[],$conn);
                    	foreach ($methodres as $res) {
                    		$paystatus .= ucfirst($res['name'])."<br />";
                    	} $html .= $paystatus; 
		        $html .= '</td>
		        <td style="text-align:right;padding:0px 7px;">Coupon Dis :</td>
		        <td style="text-align:right;padding:0px 7px;">';
		            $coupon_dis = $edit['discount']?$edit['discount']:'0 ';
		            $discount_type = $edit['discount_type'] == '0'?'%':'';
		            $html .=  $coupon_dis.$discount_type;
		       	$html .= '</td>
		    </tr>
		    <tr>
		        <td style="text-align:right;padding:0px 7px;">Discount : </td>
		        <td style="text-align:right;padding:0px 7px;">'.number_format(EXPLODE(',',$edit['dis'])[1],2)." ".((EXPLODE(',',$edit['dis'])[0] == 'pr')?'%':'').'</td>
		    </tr>'; 
				$tax_val = gettaxx($edit['tax']);
				if($tax_val>0){
		    $html .= '<tr>
		        <td style="text-align:right;padding:0px 7px;">Tax Type : </td>
		        <td style="text-align:right;padding:0px 7px;">';
						$discount = EXPLODE(',',$edit['dis'])[1];
						if($edit['taxtype'] == '0')
						$html .= " Inclusive";
						else if($edit['taxtype'] == '1')
						$html .= "Exclusive";
					$html .= '</td>
		    </tr>
		    <tr>
		        <td style="text-align:right;padding:0px 7px;"></td>
		        <td style="text-align:right;padding:0px 7px;">';
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
				$html .= '</td>
		    </tr>
		   	<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>SGST('.ucfirst(gettaxx($edit['tax'])/2).'%) : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format(getsum($edit['tax'],$value_after_discount,$edit['taxtype'])/2,2).'</td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>CGST('.gettaxx($edit['tax'])/2 .'%) : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format(getsum($edit['tax'],$value_after_discount,$edit['taxtype'])/2,2).'</td>
			</tr>';
				}else{
			$html .= '<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Tax : </strong></td>
				<td style="text-align:right;padding:0px 7px;">0</td>
			</tr>';
			}
			$html .= '<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Total : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format($edit['total'],2).'</td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Advance : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format($edit['advance'],2).'</td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Amount Paid : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format($edit['paid'],2).'</td>
			</tr>
			<tr>
				<td style="text-align:right;padding:0px 7px;"><strong>Amount Due : </strong></td>
				<td style="text-align:right;padding:0px 7px;">'.number_format($edit['due'],2).'</td>
			</tr>
		</table>
	</div>
</div>
</body></html>';

echo $html;

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
		if($result){
			foreach($result as $row) {
				return $row['tax'];
				}
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