<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['eid']) && $_POST['eid'] > 0){
	$eid = $_POST['eid'];
	$edit = query_by_id("SELECT * from `app_slider_services` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);
}else if(isset($_POST['gallery_eid']) && $_POST['gallery_eid'] > 0){
	$eid = $_POST['gallery_eid'];
	$edit = query_by_id("SELECT * from `app_gallery` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);

}else if(isset($_POST['slider_eid']) && $_POST['slider_eid'] > 0){
	$eid = $_POST['slider_eid'];
	$edit = query_by_id("SELECT * from `app_slider` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);

}else if(isset($_POST['offer_eid']) && $_POST['offer_eid'] > 0){
	$eid = $_POST['offer_eid'];
	$edit = query_by_id("SELECT * from `app_offer` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);
}else if(isset($_POST['edit_about_id']) && $_POST['edit_about_id'] > 0 && !isset($_POST['about'])){
	$eid = $_POST['edit_about_id'];
	$edit = query_by_id("SELECT * from `app_aboutus` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);
}
	
if(isset($_POST['service_id']) && $_POST['service_id'] > 0 && $_POST['edit_id']==''){
		$errors=[];
		$service_id	= $_POST['service_id'];
		$service_name	= $_POST['service_name'];
		$service_price	= $_POST['service_price'];
	    $file_name  = $_FILES['featured_image']['name'];
        $file_size  = $_FILES['featured_image']['size'];
        $file_tmp   = $_FILES['featured_image']['tmp_name'];
        $file_type  = $_FILES['featured_image']['type'];
        $file_ext   = strtolower(end(explode('.',$_FILES['featured_image']['name'])));
        $expensions= array("jpeg","png","jpg");
        if(in_array($file_ext,$expensions) === false){
           $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
        if($file_size > 2097152){
           $errors[]='File size must be less than 2 MB';
        }
		if(empty($errors)){ 
			$file_path = "../app/images/".time().$file_name;
			$file_path_insert = time().$file_name;
			move_uploaded_file($file_tmp,$file_path);
			query("INSERT INTO `app_slider_services` set `featured_image`='$file_path_insert',`service_id`='$service_id',`service_name`='$service_name',`price`='$service_price', `branch_id`='".$branch_id."' ",[],$conn);
			sleep(1);
			echo '{"data-inserted":"1"}';
		} 
	}else if(isset($_POST['service_id']) && $_POST['service_id'] > 0 &&  $_POST['edit_id'] > 0){
		$errors=[];
		$eid=$_POST['edit_id'];
		$service_id	= $_POST['service_id'];
		$service_name	= $_POST['service_name'];
		$service_price	= $_POST['service_price'];
	    $file_name  = $_FILES['featured_image']['name'];
        $file_size  = $_FILES['featured_image']['size'];
        $file_tmp   = $_FILES['featured_image']['tmp_name'];
        $file_type  = $_FILES['featured_image']['type'];
        $file_ext   = strtolower(end(explode('.',$_FILES['featured_image']['name'])));
        $expensions= array("jpeg","png","jpg");
        if(in_array($file_ext,$expensions) === false){
           $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
        if($file_size > 2097152){
           $errors[]='File size must be less than 2 MB';
        }
		if(empty($errors) &&  !empty($file_name)){ 
			$file_path = "../app/images/".time().$file_name;
			$file_path_insert = time().$file_name;
			move_uploaded_file($file_tmp,$file_path);
			query("update `app_slider_services` set `featured_image`='$file_path_insert',`service_id`='$service_id',`service_name`='$service_name',`price`='$service_price' where id='$eid' and status='1' and branch_id='".$branch_id."' ",[],$conn);
			sleep(1);
			echo '{"data-updated":"1"}';
		}else{
			query("update `app_slider_services` set `service_id`='$service_id',`service_name`='$service_name',`price`='$service_price' where id='$eid' and status='1' and branch_id='".$branch_id."' ",[],$conn);
			sleep(1);
			echo '{"data-updated":"1"}';
		} 
	}else if(count($_FILES['gallery_image']['tmp_name']) > 0){
		$errors=[];	 
		for($i=0;$i<count($_FILES['gallery_image']['tmp_name']);$i++){
			$file_name  = $_FILES['gallery_image']['name'][$i];
			$file_size  = $_FILES['gallery_image']['size'][$i];
			$file_tmp   = $_FILES['gallery_image']['tmp_name'][$i];
			$file_type  = $_FILES['gallery_image']['type'][$i];
			$file_ext   = strtolower(end(explode('.',$_FILES['gallery_image']['name'][$i])));
			$expensions= array("jpeg","png","jpg");
			if(in_array($file_ext,$expensions) === false){
			   $errors[]="extension not allowed, please choose a JPEG or PNG file.";
			}
			if($file_size > 2097152){
			   $errors[]='File size must be less than 2 MB';
			}
			if(empty($errors)){ 
				$file_path = "../app/images/".time().$file_name;
				$file_path_insert = time().$file_name;
				move_uploaded_file($file_tmp,$file_path);
				query("INSERT INTO `app_gallery` set `name`='$file_path_insert',status='1', branch_id='".$branch_id."' ",[],$conn);
			}
		}
		sleep(2);
			echo '{"data-inserted":"1"}';
	}else if(count($_FILES['slider_image']['tmp_name']) > 0){
		$errors=[];	 
		for($i=0;$i<count($_FILES['slider_image']['tmp_name']);$i++){
			$file_name  = $_FILES['slider_image']['name'][$i];
			$file_size  = $_FILES['slider_image']['size'][$i];
			$file_tmp   = $_FILES['slider_image']['tmp_name'][$i];
			$file_type  = $_FILES['slider_image']['type'][$i];
			$file_ext   = strtolower(end(explode('.',$_FILES['slider_image']['name'][$i])));
			$expensions= array("jpeg","png","jpg");
			if(in_array($file_ext,$expensions) === false){
			   $errors[]="extension not allowed, please choose a JPEG or PNG file.";
			}
			if($file_size > 2097152){
			   $errors[]='File size must be less than 2 MB';
			}
			if(empty($errors)){ 
				$file_path = "../app/images/".time().$file_name;
				$file_path_insert = time().$file_name;
				move_uploaded_file($file_tmp,$file_path);
				query("INSERT INTO `app_slider` set `name`='$file_path_insert',status='1', branch_id='".$branch_id."' ",[],$conn);
			}
		}
		sleep(2);
			echo '{"data-inserted":"1"}';
	}else if(isset($_POST['offer_name']) && $_POST['offer_edit_id']==''){
		$errors=[];
		 
		$offer_name	= $_POST['offer_name'];
		$offer_desc	= $_POST['offer_desc'];
		$image_role	= $_POST['image_role'];
	    $file_name  = $_FILES['offer_image']['name'];
        $file_size  = $_FILES['offer_image']['size'];
        $file_tmp   = $_FILES['offer_image']['tmp_name'];
        $file_type  = $_FILES['offer_image']['type'];
        $file_ext   = strtolower(end(explode('.',$_FILES['offer_image']['name'])));
        $expensions= array("jpeg","png","jpg");
        if(in_array($file_ext,$expensions) === false){
           $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
        if($file_size > 2097152){
           $errors[]='File size must be less than 2 MB';
        }
		if(empty($errors)){ 
			$file_path = "../app/images/".time().$file_name;
			$file_path_insert = time().$file_name;
			move_uploaded_file($file_tmp,$file_path);
			query("INSERT INTO `app_offer` set `name`=:offer_image,`offer_name`=:offer_name,`offer_desc`=:offer_desc,`main`=:main, `branch_id`='".$branch_id."' ",
										[
										'offer_image'=>$file_path_insert,
										'offer_name'=>$offer_name,
										'offer_desc'=>$offer_desc,
										'main'=>$image_role,
										],$conn);
			sleep(1);
			echo '{"data-inserted":"1"}';
		} 
	}else if(isset($_POST['offer_edit_id']) &&  $_POST['offer_edit_id'] > 0){
		$errors=[];
		$eid=$_POST['offer_edit_id'];
		$offer_name	= $_POST['offer_name'];
		$offer_desc	= $_POST['offer_desc'];
		$image_role	= $_POST['image_role'];
	    $file_name  = $_FILES['offer_image']['name'];
        $file_size  = $_FILES['offer_image']['size'];
        $file_tmp   = $_FILES['offer_image']['tmp_name'];
        $file_type  = $_FILES['offer_image']['type'];
        $file_ext   = strtolower(end(explode('.',$_FILES['featured_image']['name'])));
        $expensions= array("jpeg","png","jpg");
        if(in_array($file_ext,$expensions) === false){
           $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
        if($file_size > 2097152){
           $errors[]='File size must be less than 2 MB';
        }
		if(empty($errors) &&  !empty($file_name)){ 
			$file_path = "../app/images/".time().$file_name;
			$file_path_insert = time().$file_name;
			move_uploaded_file($file_tmp,$file_path);
			query("update `app_offer` set `name`=:offer_image,`offer_name`=:offer_name,`offer_desc`=:offer_desc,`main`=:main where id=:id and status='1' and branch_id='".$branch_id."'",
										[
										'offer_image'=>$file_path_insert,
										'offer_name'=>$offer_name,
										'offer_desc'=>$offer_desc,
										'main'=>$image_role,
										'id'=>$eid
										],$conn);
			sleep(1);
			echo '{"data-updated":"1"}';
		}else{
			query("update `app_offer` set `offer_name`=:offer_name,`offer_desc`=:offer_desc,`main`=:main  where id=:id and status='1' and branch_id='".$branch_id."' ",[
										 
										'offer_name'=>$offer_name,
										'offer_desc'=>$offer_desc,
										'main'=>$image_role,
										'id'=>$eid
										],$conn);
			sleep(1);
			echo '{"data-updated":"1"}';
		} 
	}else if(isset($_POST['about']) && $_POST['edit_about_id']==''){
		 
		$about	= $_POST['about'];
			query("INSERT INTO `app_aboutus` set `about_us`=:about_us, `branch_id`='".$branch_id."'",
										[
										'about_us'=>$about,
										],$conn);
			sleep(1);
			echo '{"data-inserted":"1"}';
	}else if(isset($_POST['about']) && $_POST['edit_about_id']>0){
		$about	= $_POST['about'];
		$id = $_POST['edit_about_id'];
		query("UPDATE`app_aboutus` set `about_us`=:about_us where id=:id and branch_id='".$branch_id."'",
										[
										'about_us'=>$about,
										'id'=>$id
										],$conn);
			sleep(1);
			echo '{"data-updated":"1"}';
		 
	}
	