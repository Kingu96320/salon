<?php
include_once '../includes/db_include.php';
include("../../salonSoftFiles_new/send_sms.php");
include_once 'cors.php';
$resultArr = array();
$html = '<div class="modal" tabindex="-1" id="otp_modal" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>6 Digit OTP sent on your mobile.</p>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,\'\');" maxlength="6" />
                <span class="text-danger" id="msg"></span>
              </div>
              <div class="modal-footer">
                <button type="button" id="btn-book" onclick="varifyOtp()" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$(\'#otp_modal , .modal-backdrop, #sscript\').remove();$(\'body\').removeClass(\'modal-open\');$(\'body\').css(\'padding\',\'0px\');">cancel</button>
              </div>
            </div>
          </div>
        </div>
        <div id="sscript">
        <script>
            $("#otp_modal").modal({
                backdrop: "static",
                keyboard: false
            });
            function varifyOtp(){
                var otp = $(\'#otp\').val();
                var name = $(\'#name\').val();
                var phone = $(\'#number\').val();
                var doa = $(\'#doa\').val();
                var apptime = $(\'#apptime\').val();
                var branch_id = $("input[name=\'branches\']:checked").val();
                var service = JSON.parse(localStorage.getItem(\'selected_services\'));
                if(otp != \'\'){
                    $(\'#msg\').text(\'\');
                    $.ajax({
                       url : \'https://easygymsoftware.in/new_design_salon/api/otp.php\',
                       method : \'post\',
                       dataType : \'JSON\',
                       data : { action : \'vaotp\', otp : otp, phone : phone, name : name, doa : doa, apptime : apptime, branch_id : branch_id, service : service },
                       beforeSend: function() {
                            $(\'#btn-book\').text(\'Please wait..\');
                        },
                       success : function(res){
                           if(res.status == "1"){
                               $(\'#otp_modal , .modal-backdrop, #sscript\').remove();
                               $(\'body\').removeClass(\'modal-open\');
                               $(\'body\').css(\'padding\',\'0px\');
                               $(\'#btn-book\').text(\'Submit\');
                               localStorage.clear();
                               window.location.href="thankyou.php";
                           } else if(res.status == "2"){
                               $(\'#msg\').text(res.msg);
                               $(\'#btn-book\').text(\'Submit\');
                           } else if(res.status == "3"){
                               $(\'#msg\').text(res.msg);
                               $(\'#btn-book\').text(\'Submit\');
                           }
                       }
                    });
                } else {
                    $(\'#msg\').text(\'Please Enter OTP\');
                }
            }
        </script><div>';
if(isset($_POST['action']) && $_POST['action'] == 'otp'){
    $phone = $_POST['phone'];
    $rand = rand(100000,999999);
    $check_user = query("SELECT count(*) as count FROM web_otp where phone_number='".$phone."'",[],$conn)[0]['count'];
    if($check_user){
        query("UPDATE web_otp SET otp = '".$rand."', status ='1' WHERE phone_number = '".$phone."'",[],$conn);
        $sms_data = array(
	        'otp' => $rand
	    );
        send_sms($phone,$sms_data,'appointment_booking_otp');
        echo $html;
    } else {
        $new_id = get_insert_id("INSERT INTO web_otp SET phone_number='".$phone."', otp = '".$rand."', status = '1'",[],$conn);
        if($new_id > 0){
            $sms_data = array(
    	        'otp' => $rand
    	    );
            send_sms($phone,$sms_data,'appointment_booking_otp');
            echo $html;
        }
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'vaotp'){
    $name = $_POST['name'];
    $doa = $_POST['doa'];
    $apptime = $_POST['apptime'];
    $phone = $_POST['phone'];
    $otp = $_POST['otp'];
    $moreservices = $_POST['service'];
    $branch_id = $_POST['branch_id'];
    if($branch_id == '' || $branch_id <= 0){
        $branch_id = 1;
    }
    $res = array();
    if($otp != '' && $phone != ''){
        $check_otp = query("SELECT otp FROM web_otp WHERE phone_number='".$phone."' AND status = '1'",[],$conn)[0]['otp'];
        if($check_otp != ''){
            $verify_otp = query("SELECT * FROM web_otp WHERE phone_number = '".$phone."' AND otp='".$otp."' AND status = '1'",[],$conn);
            if($verify_otp){
                //$service_data = query("SELECT name, price, cat, duration FROM service WHERE id='".$service."' AND active = '0'",[],$conn)[0];
                $get_client = query_by_id("SELECT id FROM client WHERE cont='".$phone."' AND active='0'",[],$conn)[0]['id'];
                $get_client_name = query_by_id("SELECT name FROM client WHERE cont='".$phone."' AND active='0'",[],$conn)[0]['name'];
                if($get_client_name != ''){
                    $name = $get_client_name;
                }
                if($get_client == ''){
                    $get_client = get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`active`=:active, `branch_id`='".$branch_id."'",[
                                        'name'  =>$name,
                                        'cont'  =>$phone,
                                        'gst'   =>'',
                                        'gender'=>'',
                                        'dob'   =>$dob,
                                        'aniversary'=>'',
                                        'active' =>0
                                    ],$conn);
                }
                $appdate = date('Y-m-d');
                $total_amount = 0;
                //$total_amount = $total_amount+$service_data['price'];
                $invoice_id = get_insert_id("INSERT INTO `app_invoice_".$branch_id."`(`client`,`doa`,`itime`,`role`,`dis`,`disper`,`tax`,`taxtype`,`pay_method`,`total`,`subtotal`,`bmethod`,`paid`,`due`,`notes`,`type`,`status`,`details`,`appdate`,`active`,`appuid`,`app_from`) VALUES ('$get_client','$doa','$apptime','3','".CURRENCY.",0','0','0','3','0','0','0','0','0','0','Website appointment','1','Pending','','$appdate',0,'0','1')",[],$conn);
                if($moreservices != ''){
                    for($i=0; $i < count($moreservices); $i++){
                        $res['iddd'] = $moreservices[$i]['id'];
                        $service_data2 = query("SELECT name, price, cat, duration FROM service WHERE id='".$moreservices[$i]['id']."' AND active = '0'",[],$conn)[0];
                        $total_amount = $total_amount+$service_data2['price'];
                        $app_inv_item_id = get_insert_id("INSERT INTO `app_invoice_items_".$branch_id."` set `iid`='$invoice_id',`client`='$get_client',`service`='sr,".$moreservices[$i]['id']."',`quantity`='1',`staffid`='0',`disc_row`='".CURRENCY.",0',`price`='".$service_data2['price']."',`type`='Service',`start_time`='0000-00-00 00:00:00',`end_time`='0000-00-00 00:00:00',`app_date`='$doa',`active`=0",[],$conn);
                        if($provider == ''){
                            query("INSERT INTO `app_multi_service_provider` set `iid`='$invoice_id',`aii_staffid`='$app_inv_item_id',`service_cat`='".$service_data2['cat']."',`service_name`='sr,".$moreservices[$i]['id']."',`service_provider`='0',`status`='1'",[],$conn);
                        } else {
                            query("INSERT INTO `app_multi_service_provider` set `iid`='$invoice_id',`aii_staffid`='$app_inv_item_id',`service_cat`='".$service_data2['cat']."',`service_name`='sr,".$moreservices[$i]['id']."',`service_provider`='0',`status`='1'",[],$conn);
                        }
                    }
                }
                
                query("UPDATE app_invoice_$branch_id SET total = '".$total_amount."', subtotal = '".$total_amount."', due = '".$total_amount."' WHERE id = '".$invoice_id."'",[],$conn);
                query("DELETE FROM web_otp WHERE phone_number = '".$phone."' AND otp='".$otp."' AND status = '1'",[],$conn);
                
                $sms_data = array(
        	        'name' => $name,
        	        'date' => date('d-m-Y',strtotime($doa)),
        	        'time' => date('h:i a',strtotime($apptime)),
        	        'salon_name' => systemname()
        	    );
                
                send_sms($phone,$sms_data,'online_appointment_booking_sms');
                $res['status'] = '1';
                $res['msg'] = 'Valid user';
            } else {
                $res['status'] = '2';
                $res['msg'] = 'Invalid OTP';   
            }
        } else {
            $res['status'] = '2';
            $res['msg'] = 'Invalid OTP';   
        }
    } else {
        $res['status'] = '3';
        $res['msg'] = 'Please insert OTP';
    }
    echo json_encode($res);
}


?>