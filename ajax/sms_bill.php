<?php
include_once '../includes/db_include.php';
// include("../../salonSoftFiles_new/send_sms.php");
$branch_id = $_SESSION['branch_id'];
// function to send bill sms
if(isset($_POST['action']) && $_POST['action'] == 'invoice_sms'){
    $invoice_id = $_POST['inv'];
    $system = systemname();
    $data = query_by_id("SELECT i.id as inv_id, i.doa, i.total, c.name, c.cont, c.id as client_id, c.referral_code  FROM `invoice_".$branch_id."` i LEFT JOIN client c ON i.client = c.id WHERE i.id = $invoice_id and i.branch_id='".$branch_id."'",[],$conn)[0];

    $ep = query_by_id("SELECT SUM(reward_point) as total FROM invoice_items_$branch_id WHERE iid='".$invoice_id."'",[],$conn)[0]['total'];
    $rew_point = get_reward_points($data['client_id'],$client_info['referral_code'],$invoice_id);

    if($ep <= 0 && $rew_point <= 0){
        $sms_data = array(
            'name' => $data['name'],
            'amount' => $data['total'],
            'date' => my_date_format($data['doa']),
            'inv_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/invoice.php?invMencr=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'feedback_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/client_feedback.php?encinv=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'salon_name' => $system
        );
        $sms_status = send_sms($data['cont'],$sms_data,'billing_sms');
    } else if($ep > 0){
        $sms_data = array(
            'name' => $data['name'],
            'amount' => $data['total'],
            'date' => my_date_format($data['doa']),
            'inv_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/invoice.php?invMencr=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'feedback_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/client_feedback.php?encinv=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'salon_name' => $system,
            'reward_point' => $ep,
            'pending_point' => $rew_point
        );
        $sms_status = send_sms($data['cont'],$sms_data,'billing_sms_with_earned_pending_point');
    } else if($rew_point > 0){
        $sms_data = array(
            'name' => $data['name'],
            'amount' => $data['total'],
            'date' => my_date_format($data['doa']),
            'inv_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/invoice.php?invMencr=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'feedback_link' => short_url("https://".$_SERVER['HTTP_HOST']."/".explode("/",$_SERVER['PHP_SELF'])[1]."/client_feedback.php?encinv=".encrypt_url($invoice_id)."&invshopid=".encrypt_url($branch_id)),
            'salon_name' => $system,
            'pending_point' => $rew_point
        );
        $sms_status = send_sms($data['cont'],$sms_data,'billing_sms_with_pending_point');
    }
    
    // echo $sms_status."<br />";
    // print_r($sms_status)."<br />";
    
    if($sms_status == 'success'){
        echo '1';   
    } else {
        echo '0';
    }
}

// function for listup services to add product usage

if(isset($_POST['action']) && $_POST['action'] == 'product_usage'){
    $invoice_id = $_POST['inv'];
    $query = "SELECT ii.id as siid, ii.iid as inv_id, ii.type, s.id as service_id, s.name, imsp.service_provider as sp_id FROM invoice_items_".$branch_id." ii LEFT JOIN service s ON CONCAT('sr',',',s.id) = ii.service LEFT JOIN invoice_multi_service_provider imsp ON ii.id = imsp.ii_id WHERE iid = $invoice_id AND ii.type='service' and ii.branch_id='".$branch_id."' and imsp.branch_id='".$branch_id."'";
    $data = query_by_id($query,[],$conn);
    $sp_ids = query_by_id("SELECT GROUP_CONCAT(service_provider) as provider FROM invoice_multi_service_provider WHERE inv='".$invoice_id."' and branch_id='".$branch_id."'",[],$conn)[0];
    
    if($data){
        foreach($data as $res){
            $saved_cons = query_by_id("SELECT * FROM product_usage WHERE invoice_id='".$res['inv_id']."' AND service_id='".$res['service_id']."' AND ii_id='".$res['siid']."' and branch_id='".$branch_id."'",[],$conn);
            if($saved_cons){
                $count = 0;
                foreach($saved_cons as $res1){
                    ?>
                        <tr class="product_usage_row" id="pro_row_<?= $res['siid'] ?>">
                            <td>
                                <input type="text" name="sname" value="<?= $res1['service_name'] ?>" readonly class="sname form-control" />
                                <input type="hidden" name="serName" value="<?= $res1['service_name'] ?>" class="serName" />
                                <input type="hidden" name="serId" value="<?= $res1['service_id'] ?>" class="serId" />
                                <input type="hidden" name="invId" value="<?= $res1['invoice_id'] ?>" class="invId"/>
                                <input type="hidden" name="spId" value="<?= $res1['service_provider'] ?>" class="spId"/>
                                <input type="hidden" name="siid" value="<?= $res1['ii_id'] ?>" class="siid"/>
                                <input type="hidden" name="stock_id" class="stock_id" value="<?= $res1['stock_id'] ?>" />
                                <span class="available hidden"></span>
                                <span class="available_show hidden"></span>
                            </td>
                            <td>
                                <input type="text" name="pname" class="pro form-control" value="<?= $res1['product_name'] ?>"  placeholder="Product name" />
                                <input type="hidden" name="pid" class="product_id_prod form-control" value="<?= $res1['product_id'] ?>" />
                                <input type="hidden" name="product_name" class="product_name form-control" value="<?= $res1['product_name'] ?>" />
                            </td>
                            <td>
                                <input type="number" min="0" name="qty" class="qt_prod form-control" value="<?= $res1['quantity'] ?>" placeholder="0" />
                            </td>
                            <td>
                                <select class="form-control v_unit_prod" name="unit" disabled>
                                    <?php $sql	="select * from units where active=0 order by id asc";
            							$result	=query_by_id($sql,[],$conn);
            							foreach($result as $row)
            							{
            							?>
            							<option value="<?=$row['id']?>" <?= $res1['unit']==$row['id']?'selected':'' ?>><?=$row['name']?></option>
            							<?php 
            							}  
            					    ?>	
                                </select>
                                <select class="form-control v_unit_prod" name="unit" style="display:none;" readonly>
                                    <?php $sql	="select * from units where active=0 order by id asc";
            							$result	=query_by_id($sql,[],$conn);
            							foreach($result as $row)
            							{
            							?>
            							<option value="<?=$row['id']?>" <?= $res1['unit']==$row['id']?'selected':'' ?>><?=$row['name']?></option>
            							<?php 
            							}  
            					    ?>	
                                </select>
                            </td>
                            <td>
                                <select name="service_provider" class="form-control staff_service" required>
        							<option value="">Service provider</option>          
        							<?php 
        								$sql="SELECT * FROM `beauticians` where active='0' and type='2' and branch_id='".$branch_id."' and id IN (".$sp_ids['provider'].") order by name asc";
        								$result = query_by_id($sql,[],$conn);
        								foreach ($result as $row){
        								    ?>    
        							    	<option value="<?= $row['id'] ?>" <?= $row['id']==$res1['service_provider']?'selected':'' ?>><?=$row['name']?></option>
        							    <?php } ?>
        						</select>
                            </td>
                            <td class="btn-action">
                                <?php if($count == 0){ ?>
								<button style="margin-left:0px;" class="btn btn-add btn-plus btn-success add-pro-used" onclick="duplicate('<?= $res['siid'] ?>')" type="button">
									<span class="glyphicon-plus"></span>
								</button>
								<?php } else { ?>
								    <button style="margin-left:0px;" onclick="$(this).parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus"></span></button>
								<?php } ?>
								
                            </td>
                        </tr>                        
                    <?php
                    $count++;
                }
            } else {
            ?>
                <tr class="product_usage_row" id="pro_row_<?= $res['siid'] ?>">
                    <td>
                        <input type="text" name="sname" value="<?= $res['name'] ?>" readonly class="sname form-control" />
                        <input type="hidden" name="serName" value="<?= $res['name'] ?>" class="serName" />
                        <input type="hidden" name="serId" value="<?= $res['service_id'] ?>" class="serId" />
                        <input type="hidden" name="invId" value="<?= $res['inv_id'] ?>" class="invId"/>
                        <input type="hidden" name="spId" value="<?= $res['sp_id'] ?>" class="spId"/>
                        <input type="hidden" name="siid" value="<?= $res['siid'] ?>" class="siid"/>
                        <input type="hidden" name="stock_id" class="stock_id" />
                        <span class="available hidden"></span>
                        <span class="available_show hidden"></span>
                    </td>
                    <td>
                        <input type="text" name="pname" class="pro form-control"  placeholder="Product name" />
                        <input type="hidden" name="pid" class="product_id_prod form-control" />
                        <input type="hidden" name="product_name" class="product_name form-control" />
                    </td>
                    <td>
                        <input type="number" min="0" name="qty" class="qt_prod form-control" placeholder="0" />
                    </td>
                    <td>
                        <select class="form-control v_unit_prod" name="unit" disabled>
                            <?php $sql	="select * from units where active=0 order by id asc";
    							$result	=query_by_id($sql,[],$conn);
    							foreach($result as $row)
    							{
    							?>
    							<option value="<?=$row['id']?>"><?=$row['name']?></option>
    							<?php 
    							}  
    					    ?>	
                        </select>
                        <select class="form-control v_unit_prod" name="unit" style="display:none;" readonly>
                            <?php $sql	="select * from units where active=0 order by id asc";
    							$result	=query_by_id($sql,[],$conn);
    							foreach($result as $row)
    							{
    							?>
    							<option value="<?=$row['id']?>"><?=$row['name']?></option>
    							<?php 
    							}  
    					    ?>	
                        </select>
                    </td>
                    <td>
                        <select name="service_provider" class="form-control staff_service" required>
							<option value="">Service provider</option>          
							<?php 
								$sql="SELECT * FROM `beauticians` where active='0' and type='2' and branch_id='".$branch_id."' and id IN (".$sp_ids['provider'].") order by name asc";
								$result = query_by_id($sql,[],$conn);
								foreach ($result as $row){
								    ?>    
							    	<option value="<?= $row['id'] ?>" <?= $row['id']==$res['sp_id']?'selected':'' ?>><?=$row['name']?></option>
							    <?php } ?>
						</select>
                    </td>
                     <td class="btn-action">
						<button style="margin-left:0px;" class="btn btn-add btn-plus btn-success add-pro-used" onclick="duplicate('<?= $res['siid'] ?>')" type="button">
							<span class="glyphicon-plus"></span>
						</button>
						
                    </td>
                </tr>
                <?php
            }
        }
    }
}

?>