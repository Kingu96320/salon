<?php 
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['action']) && $_GET['action'] == 'show_refferal'){ 
		$client_id = $_GET['client_id'];
		$referral_code=query("SELECT referral_code FROM `client` WHERE active='0' and id='$client_id' and branch_id='".$branch_id."'",[],$conn)[0];
	?>
	<style>
	    .ref-code{
	        max-width: 250px;
            background: linear-gradient(45deg, #1de5e2 1%,#b389f7 100%);
            padding: 10px;
            font-size: 20px;
            margin: 0px auto;
            border-radius: 5px;
            color: #fff;
            margin-top:10px;
            border:none;
            text-align:center;
	    }
	    #copytoclipboard{
	        font-size: 24px;
            cursor: pointer;
            float: right;
            margin-left: 20px;
            margin-top: 18px;
	    }
	</style>
	<div class="modal-body">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
				    <h3 class="text-center" style="color: #002e54;margin-bottom: 10px;">Invite your Friends &amp; Earn points</h3>
				    <p class="text-center" style="font-size:16px;">You & your friend will get <?= REFERRAL_POINTS ?> points on <br />your friend first billing</p>
				    <img src="img/reff-icon.png" class="img-responsive" style="max-width:200px;margin:0px auto;" />
				    <p class="text-center">Your referral code</p>
				    <div class="text-center" style="position:relative;max-width:300px;margin:0px auto;">
				        <input type="text" readonly style="max-width:250px;" class="ref-code" id="reff_code" value="<?= $referral_code['referral_code'] ?>">
				        <span id="copytoclipboard" onclick="copyRefCode()" title="Copy"><i class="fa fa-clone" aria-hidden="true"></i></span>
				    </div>
				</div>
			</div>
		</div>
	</div> 
</div>
<script>
    function copyRefCode() {
      var copyText = document.getElementById("reff_code");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
      toastr.success("Code copied.");
    }
</script>
<?php  } ?>

<?php
if(isset($_GET['action']) && $_GET['action'] == 'update_refferal_code'){
    $client_id = $_GET['client_id'];
    $ref_code = $_GET['ref_code'];
    query("UPDATE client SET referral_code = '".$ref_code."' WHERE id='".$client_id."' and branch_id='".$branch_id."'",[],$conn);
    echo '1';
}

?>