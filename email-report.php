<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$date = date('Y-m-d');
$time = date('h:i A');
// query to get salon logo
$system = query_by_id("SELECT logo, salon, address, phone, website, email, gst FROM system WHERE id = '".$branch_id."' and branch_id='".$branch_id."'",[],$conn)[0];

// header 

$html = '<table style"width:100%;display:block;">
            <tr>
                <td style="width:300px;">
                    <img src="'.$system['logo'].'" style="max-width:120px;" />
                </td>
                <td style="width:700px;text-align:right;">
                    <span><strong>Report Date :</strong> </span> '.my_date_format($date).'
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width:1000px;text-align:center;">
                    <h1 style="text-transform:uppercase;">Daily Report</h1>
                </td>
            </tr>
        </table><br />
';

// Company details

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:13px;width:150px;"><strong>Salon name </strong></td>
                <td style="font-size:13px;"> : '.$system['salon'].'</td>
            </tr>
            <tr>
                <td style="font-size:13px;"><strong>Phone number </strong></td>
                <td style="font-size:13px;"> : '.$system['phone'].'</td>
            </tr>
            <tr>
                <td style="font-size:13px;"><strong>Email </strong></td>
                <td style="font-size:13px;"> : '.$system['email'].'</td>
            </tr>
            <tr>
                <td style="font-size:13px;"><strong>Website </strong></td>
                <td style="font-size:13px;"> : '.$system['website'].'</td>
            </tr>';
    if($system['gst'] !=''){
        $html .='<tr>
                <td style="font-size:13px;"><strong>Gst no. </strong></td>
                <td style="font-size:13px;"> : '.$system['gst'].'</td>
            </tr>';
    }
    $html .='<tr>
                <td style="font-size:13px;"><strong>Address </strong></td>
                <td style="font-size:13px;"> : '.$system['address'].'</td>
            </tr>
</table><hr />
';

// query to get enquiry reports data
$total_enq = query_by_id("SELECT COUNT(*) as total FROM enquiry WHERE regon = '".$date."' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$enq = query_by_id("SELECT * FROM enquiry WHERE regon = '".$date."' and branch_id='".$branch_id."'",[],$conn);
$pending = query_by_id("SELECT COUNT(*) as total FROM enquiry WHERE regon = '".$date."' AND leadstatus = 'Pending' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$converted = query_by_id("SELECT COUNT(*) as total FROM enquiry WHERE regon = '".$date."' AND leadstatus = 'Converted' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$closed = query_by_id("SELECT COUNT(*) as total FROM enquiry WHERE regon = '".$date."' AND leadstatus = 'Close' and branch_id='".$branch_id."'",[],$conn)[0]['total'];

// Enquiry report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Enquiry Report</th>
            </tr>
</table><br />
';

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Total Enquiries </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$total_enq.'</td>
                <td style="font-size:14px;width:150px;"><strong>Pending Enquiries </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$pending.'</td>
                <td style="font-size:14px;width:150px;"><strong>Converted Enquiries </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$converted.'</td>
                <td style="font-size:14px;width:150px;"><strong>Closed Enquiries </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$closed.'</td>
            </tr>
</table><br />';

if($total_enq > 0){
    $html .= '<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Email</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Phone</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Follow date</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Lead type</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Enquiry for</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Source</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Enquiry status</strong></td>
                </tr>';
                foreach($enq as $res){
                    $sourceofclient = query_by_id("SELECT leadsource from `client` where id=:id and branch_id='".$branch_id."'",["id"=>$res['client_id']],$conn)[0];
                    $html .='<tr>
                                <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['id'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['customer'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['email'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['cont'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.my_date_format($res['datefollow']).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['type'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.getEnquiryfor($res['enquiry']).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$sourceofclient['leadsource'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['leadstatus'].'</td>
                             </tr>
                    ';
                }    
    $html .='</table><br /><hr />';
}

// queries to get appointment reports data

$appointments = query_by_id("SELECT count(*) as total FROM app_invoice_".$branch_id." WHERE appdate = '".$date."' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$today_app = query_by_id("SELECT count(*) as total FROM app_invoice_".$branch_id." WHERE doa = '".$date."' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$pending_app = query_by_id("SELECT count(*) as total FROM app_invoice_".$branch_id." WHERE doa = '".$date."' AND status='Pending' AND bill_created_status = '0' AND active = '0' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$billed_app = query_by_id("SELECT count(*) as total FROM app_invoice_".$branch_id." WHERE (bill_created_status = '1' || status = 'Billed') AND DATE(updatetime) = '".$date."' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$cancel_app = query_by_id("SELECT count(*) as total FROM app_invoice_".$branch_id." WHERE doa = '".$date."' AND status = 'Cancelled' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$app_amount = query_by_id("SELECT SUM(subtotal) as subtotal, SUM(total) as total, SUM(paid) as paid, SUM(due) as due, SUM(subtotal-total) as discount FROM app_invoice_".$branch_id." WHERE doa='".$date."' AND active = '0' AND status != 'Cancelled' and branch_id='".$branch_id."'",[],$conn)[0];

// Appointment report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Appointment Report</th>
            </tr>
</table><br />';

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Booked Appointments </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$appointments.'</td>
                <td style="font-size:14px;width:150px;"><strong>Today Appointments  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$today_app.'</td>
                <td style="font-size:14px;width:150px;"><strong>Pending  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$pending_app.'</td>
                <td style="font-size:14px;width:150px;"><strong>Billed  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$billed_app.'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Cancelled  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$cancel_app.'</td>
                <td style="font-size:14px;width:150px;"><strong>Total amount </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($app_amount['subtotal'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Discount </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($app_amount['discount'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Amount to paid </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($app_amount['total'],2).'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Advance amount  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($app_amount['paid'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Pending amount  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($app_amount['due'],2).'</td>
            </tr>
</table><br />';

if($appointments > 0){
	$booked_app = query_by_id("SELECT  ai.notes,pm.name as pay_method_name, ai.subtotal, (ai.subtotal-ai.total) as discount, c.id as client, ai.status, ai.paid,ai.due,ai.bill_created_status,ai.ss_created_status,ai.doa,ai.appdate,ai.id,ai.total,ai.pay_method,c.name,c.cont from `app_invoice_".$branch_id."` ai "
					." LEFT JOIN `client` c on c.id=ai.client "
					." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
					." where ai.active=0 and appdate='".$date."' and ai.branch_id='".$branch_id."' order by ai.id ASC",[],$conn);
						
    $html .='<h3 style="font-size:14px;">Booked appointment details : </h3>';
    $html .= '<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>App. date</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Customer name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Phone</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Subtotal</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Discount</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Total</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Paid</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Pending</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Status</strong></td>
                </tr>';
                foreach($booked_app as $res){
                    if($res['bill_created_status'] == '1'){
                        $status = 'Billed';
                    } else {
                        $status = $res['status'];
                    }
                    $html .='<tr>
                                <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['id'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.my_date_format($res['doa']).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['name'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['cont'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['subtotal'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['discount'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['total'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['paid'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['due'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$status.'</td>
                             </tr>
                    ';
                }    
    $html .='</table><br />';
}

if($today_app > 0){
	$today_app = query_by_id("SELECT  ai.notes,pm.name as pay_method_name, ai.subtotal, (ai.subtotal-ai.total) as discount, c.id as client, ai.status, ai.paid,ai.due,ai.bill_created_status,ai.ss_created_status,ai.doa,ai.appdate,ai.id,ai.total,ai.pay_method,c.name,c.cont from `app_invoice_".$branch_id."` ai "
					." LEFT JOIN `client` c on c.id=ai.client "
					." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
					." where ai.active=0 and doa='".$date."' and ai.branch_id='".$branch_id."' order by ai.id ASC",[],$conn);
						
    $html .='<h3 style="font-size:14px;">Today appointment details : </h3>';
    $html .= '<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>App. date</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Customer name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Phone</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Subtotal</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Discount</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Total</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Paid</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Pending</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Status</strong></td>
                </tr>';
                foreach($today_app as $res){
                    if($res['bill_created_status'] == '1'){
                        $status = 'Billed';
                    } else {
                        $status = $res['status'];
                    }
                    $html .='<tr>
                                <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['id'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.my_date_format($res['doa']).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['name'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['cont'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['subtotal'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['discount'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['total'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['paid'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['due'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$status.'</td>
                             </tr>
                    ';
                }    
    $html .='</table><br /><hr />';
}

// queries to get Invoice reports data

$invoice = query_by_id("SELECT count(*) as total FROM invoice_".$branch_id." WHERE doa = '".$date."' and branch_id='".$branch_id."'",[],$conn)[0]['total'];
$inv_amount = query_by_id("SELECT SUM(subtotal) as subtotal, SUM(total) as total, SUM(paid) as paid, SUM(due) as due, SUM(subtotal-total) as discount, SUM(advance) as advance FROM invoice_".$branch_id." WHERE doa='".$date."' AND active = '0' and branch_id='".$branch_id."'",[],$conn)[0];

// Billing report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Billing Report</th>
            </tr>
</table><br />';

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Generated Invoice </strong></td>
                <td style="font-size:14px;width:100px;"> : '.$invoice.'</td>
                <td style="font-size:14px;width:150px;"><strong>Total amount </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['subtotal'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Discount </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['discount'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Amount to paid </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['total'],2).'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Paid amount  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['paid'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Advance amount  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['advance'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Pending amount  </strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($inv_amount['due'],2).'</td>
            </tr>
</table><br />';

if($invoice > 0){
	$today_inv = query_by_id("SELECT i.*,pm.name as payment_mode,c.name as c_name,c.cont, (i.subtotal-i.total) as dis FROM `invoice_".$branch_id."` i "
	                          ."LEFT JOIN `client` c on c.id=i.client "
	                          ."LEFT JOIN `payment_method` pm on pm.id=i.pay_method "
	                          ."WHERE i.active=0 and doa='".$date."' and i.branch_id='".$branch_id."' order by i.id ASC",[],$conn);
    $html .='<h3 style="font-size:14px;">Today billing details : </h3>';
    $html .= '<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Bill date</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Customer name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Phone</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Subtotal</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Discount</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Total</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Advance</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Paid</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Pending</strong></td>
                </tr>';
                foreach($today_inv as $res){
                    $html .='<tr>
                                <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['id'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.my_date_format($res['doa']).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['c_name'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.$res['cont'].'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['subtotal'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['dis'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['total'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['advance'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['paid'],2).'</td>
                                <td style="font-size:13px;padding:5px 5px;">'.number_format($res['due'],2).'</td>
                             </tr>
                    ';
                }    
    $html .='</table><br /><hr />';
}

// Queries to get services report data

$services = query_by_id("SELECT count(*) as total, SUM(quantity) as qty, SUM(price) as price FROM invoice_items_".$branch_id." WHERE date(start_time) = '".$date."' AND type = 'Service' and branch_id='".$branch_id."'",[],$conn)[0];

// Services report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Services Report</th>
            </tr>
</table><br />';

if($services['qty'] != ''){
    $qty = $services['qty'];
} else {
    $qty = '0';
}

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Delivered services</strong></td>
                <td style="font-size:14px;width:100px;"> : '.$services['total'].'</td>
                <td style="font-size:14px;width:150px;"><strong>Total quantity</strong></td>
                <td style="font-size:13px;width:100px;"> : '.$qty.'</td>
                <td style="font-size:14px;width:150px;"><strong>Total Amount</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($services['price'],2).'</td>
                <td style="font-size:14px;width:150px;"></td><td style="font-size:13px;width:100px;"></td>
            </tr>
</table><br />';
if($services['total'] > 0){
    $sep_ser = query_by_id("SELECT SUM(ii.quantity) as total_services, s.name as service_name, SUM(ii.price) as service_amount FROM invoice_items_".$branch_id." ii "
                           ."LEFT JOIN service s ON SUBSTRING_INDEX(ii.service,',',-1) = s.id "
                           ."WHERE date(start_time) = '".$date."' AND type = 'Service' and ii.branch_id='".$branch_id."' GROUP BY ii.service",[],$conn);
    $scount = 1;
    $html .='<table style="width:100%;display:block;">
                <tr>
                    <td style="font-size:13px;width:20px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Service name</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Total quantity</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Total amount</strong></td>
            </tr>';
    foreach($sep_ser as $res){
        $html .= '<tr>
                    <td style="font-size:13px;width:20px;text-align:center;">'.$scount.'</td>
                    <td style="font-size:13px;width:250px;">'.$res['service_name'].'</td>
                    <td style="font-size:13px;width:250px;">'.$res['total_services'].'</td>
                    <td style="font-size:13px;width:250px;">'.number_format($res['service_amount'],2).'</td>
        </tr>';
        $scount++;
    }
    $html .='</table><br />';
}

if($services['total'] > 0){
   $ser_detail = query_by_id("SELECT ii.iid as inv_id, ii.price, ii.quantity, s.name as service_name, c.name as client_name  FROM invoice_items_".$branch_id." ii "
                             ."LEFT JOIN service s ON SUBSTRING_INDEX(ii.service,',',-1) = s.id "
                             ."LEFT JOIN client c ON c.id = ii.client "
                             ."WHERE date(start_time) = '".$date."' AND type = 'Service' and ii.branch_id='".$branch_id."' ORDER BY ii.iid ASC",[],$conn); 
    $scount = 1;
    $html .='<h3 style="font-size:14px;">Services details : </h3>';
    $html .='<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Service name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Quantity</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Amount</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Customer name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>Invoice id</strong></td>
                </tr>';
            foreach($ser_detail as $res){
                $html .='<tr>
                            <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$scount.'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['service_name'].'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['quantity'].'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.number_format($res['price'],2).'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['client_name'].'</td>
                            <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['inv_id'].'</td>
                </tr>';
                $scount++;
            }    
    $html .='</table><br /><hr />';
}


// Queries to get products report data

$products = query_by_id("SELECT count(*) as total, SUM(quantity) as qty, SUM(price) as price FROM invoice_items_".$branch_id." WHERE date(start_time) = '".$date."' AND type = 'Product' and branch_id='".$branch_id."'",[],$conn)[0];

// Services report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Products Report</th>
            </tr>
</table><br />';

if($products['qty'] != ''){
    $qty = $products['qty'];
} else {
    $qty = '0';
}

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Delivered products</strong></td>
                <td style="font-size:14px;width:100px;"> : '.$products['total'].'</td>
                <td style="font-size:14px;width:150px;"><strong>Total quantity</strong></td>
                <td style="font-size:13px;width:100px;"> : '.$qty.'</td>
                <td style="font-size:14px;width:150px;"><strong>Total Amount</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($products['price'],2).'</td>
                <td style="font-size:14px;width:150px;"></td><td style="font-size:13px;width:100px;"></td>
            </tr>
</table><br />';
if($products['total'] > 0){
    $sep_ser = query_by_id("SELECT SUM(ii.quantity) as total_products, p.name as product_name, SUM(ii.price) as product_amount FROM invoice_items_".$branch_id." ii "
                           ."LEFT JOIN products p ON SUBSTRING_INDEX(ii.service,',',-1) = p.id "
                           ."WHERE date(start_time) = '".$date."' AND type = 'Product' and ii.branch_id='".$branch_id."' GROUP BY ii.service",[],$conn);
    $scount = 1;
    $html .='<table style="width:100%;display:block;">
                <tr>
                    <td style="font-size:13px;width:20px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Service name</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Total quantity</strong></td>
                    <td style="font-size:13px;width:250px;"><strong>Total amount</strong></td>
            </tr>';
    foreach($sep_ser as $res){
        $html .= '<tr>
                    <td style="font-size:13px;width:20px;text-align:center;">'.$scount.'</td>
                    <td style="font-size:13px;width:250px;">'.$res['product_name'].'</td>
                    <td style="font-size:13px;width:250px;">'.$res['total_products'].'</td>
                    <td style="font-size:13px;width:250px;">'.number_format($res['product_amount'],2).'</td>
        </tr>';
        $scount++;
    }
    $html .='</table><br />';
}

if($products['total'] > 0){
   $ser_detail = query_by_id("SELECT ii.iid as inv_id, ii.price, ii.quantity, p.name as product_name, c.name as client_name  FROM invoice_items_".$branch_id." ii "
                             ."LEFT JOIN products p ON SUBSTRING_INDEX(ii.service,',',-1) = p.id "
                             ."LEFT JOIN client c ON c.id = ii.client "
                             ."WHERE date(start_time) = '".$date."' AND type = 'Product' and ii.branch_id='".$branch_id."' ORDER BY ii.iid ASC",[],$conn); 
    $scount = 1;
    $html .='<h3 style="font-size:14px;">Services details : </h3>';
    $html .='<table style="width:100%;display:block;border-collapse:collapse;" border="1">
                <tr>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>#.</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Service name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Quantity</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Amount</strong></td>
                    <td style="font-size:13px;padding:5px 5px;"><strong>Customer name</strong></td>
                    <td style="font-size:13px;padding:5px 5px;text-align:center;"><strong>Invoice id</strong></td>
                </tr>';
            foreach($ser_detail as $res){
                $html .='<tr>
                            <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$scount.'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['product_name'].'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['quantity'].'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.number_format($res['price'],2).'</td>
                            <td style="font-size:13px;padding:5px 5px;">'.$res['client_name'].'</td>
                            <td style="font-size:13px;padding:5px 5px;text-align:center;">'.$res['inv_id'].'</td>
                </tr>';
                $scount++;
            }    
    $html .='</table><br /><hr />';
}


// Queries to get sales report data

$subtotal_sale_amount = query_by_id("SELECT sum(subtotal) as total from `invoice_".$branch_id."` where doa='".$date."' and active=0 and branch_id='".$branch_id."'",[],$conn)[0];
$product_sale = query_by_id("SELECT sum(ii.price) as total from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa='".$date."' and ii.type='Product' and i.active=0 and ii.branch_id='".$branch_id."'",[],$conn)[0];
$service_sale = query_by_id("SELECT sum(ii.price) as total from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa='".$date."' and ii.type='Service' and i.active=0 and ii.branch_id='".$branch_id."'",[],$conn)[0];

// Discount code start
$total_dis = 0;
$dis = query_by_id("SELECT paid, dis, subtotal, total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'",[],$conn);
$cou_dis = query_by_id("SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." ON coupons.id = invoice.coupon WHERE invoice.doa='".$date."' and invoice.active=0 and coupons.branch_id='".$branch_id."'",[],$conn);
if($dis){
    foreach($dis as $res){
        $discount = explode(",",$res['dis']);
        if($discount['0'] == CURRENCY){
            $total_dis = $total_dis + $discount['1'];
        } else if($discount['0'] == 'pr'){
            if($discount['0'] != 0){
	            $dis_price = ($res['subtotal']-$res['total']);
	            $total_dis = $total_dis+$dis_price;
            }
        }
    }
}
if($cou_dis){
    foreach($cou_dis as $res){
        $total_dis = $total_dis + $res['discount'];
    }
}
// Discount code end

// Expense account code start

$expense = query_by_id("SELECT sum(amount) as total FROM `expense` where active=0 and date='".$date."' and branch_id='".$branch_id."'",[],$conn)[0];
$ex_total = 0;
if($expense){
    if($expense['total'] != ''){
        $ex_total = $expense['total'];
    } else {
        $ex_total = 0;
    }
}

// Expense account code end

$cash_report = query_by_id("SELECT sum(mpm.amount_paid + i.advance) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='1' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$ewallet = query_by_id("SELECT sum(mpm.amount_paid + i.advance) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='7' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$credit_debit_card = query_by_id("SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='3' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$cheque = query_by_id("SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='4' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$online_payment = query_by_id("SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='5' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$paytm = query_by_id("SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa = '".$date."' and mpm.payment_method='6' and i.active=0 and i.branch_id='".$branch_id."'",[],$conn)[0];
$pending_amount = query_by_id("SELECT sum(due) as total from `invoice_".$branch_id."` where doa='".$date."' and active=0 and branch_id='".$branch_id."'",[],$conn)[0];

$net_collaction = query_by_id("SELECT sum(subtotal) as total,sum(bpaid) as paid FROM `invoice_".$branch_id."` where active=0 and doa='".$date."' and branch_id='".$branch_id."'",[],$conn)[0];
if($net_collaction['total'] != ''){ $ncol = $net_collaction['total']; } else { $ncol = 0; }
$credit_payment = query_by_id("SELECT sum(credit) as credit FROM `payments` WHERE date='".$date."' and active=0 and branch_id='".$branch_id."'",[],$conn)[0];
if($credit_payment['credit'] != ''){ $credit = $credit_payment['credit']; } else { $credit = 0; }
$vendor_payment = query_by_id("SELECT sum(debit) as debit FROM `payments` WHERE date='".$date."' and active=0 and branch_id='".$branch_id."'",[],$conn)[0];
if($vendor_payment['debit'] != ''){ $debit = $vendor_payment['debit']; } else { $debit = 0; }

$ex = $ex_total;
$ex = $ex+$debit;
$sum = $ncol-($ex+$total_dis);

// Sales report

$html .= '<table style="width:100%;display:block;">
            <tr>
                <th style="width:1000px;text-align:center;font-size:24px;">Sales Report</th>
            </tr>
</table><br />';

$html .= '<table style="width:100%;display:block;">
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Total sale amount</strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($subtotal_sale_amount['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Product Sales</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($product_sale['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Service Sales</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($service_sale['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Cash Collection</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($cash_report['total'],2).'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Total Discount</strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($total_dis,2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Credit/Debit Card Sales</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($credit_debit_card['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Cheque Sales</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($cheque['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>e-Wallet Sales</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($ewallet['total'],2).'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Online Payment</strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($online_payment['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Paytm</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($paytm['total'],2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Expenses amount</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($ex_total,2).'</td>
                <td style="font-size:14px;width:150px;"><strong>Pending amount</strong></td>
                <td style="font-size:13px;width:100px;"> : '.number_format($pending_amount['total'],2).'</td>
            </tr>
            <tr>
                <td style="font-size:14px;width:150px;"><strong>Net Earnings</strong></td>
                <td style="font-size:14px;width:100px;"> : '.number_format($sum,2).'</td>
                <td style="font-size:14px;width:150px;"><strong></strong></td>
                <td style="font-size:13px;width:100px;"></td>
                <td style="font-size:14px;width:150px;"><strong></strong></td>
                <td style="font-size:13px;width:100px;"></td>
                <td style="font-size:14px;width:150px;"></td>
                <td style="font-size:13px;width:100px;"></td>
            </tr>
</table>';

?>
<?php
    ob_clean();
    include("../salonSoftFiles_new/pdf_files/mpdf.php");
    $filename = "Daily-report.pdf";
    $mpdf = new mPDF('', 'Letter', 0, '', 5.7, 5.7, 3, 5.7, 8, 8);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
    $stylesheet = file_get_contents('mpdfstyletables.css');
    $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
    $mpdf->WriteHTML($html, 2);
    $mpdf->Output($filename, 'I');
    exit;
?>
