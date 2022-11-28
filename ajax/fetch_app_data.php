<?php 
	require_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['page']) && $_GET['page']=='featured_services')
	{
		$sql="SELECT * from `app_slider_services` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row){
			$arr[] = array(
			'f_service'	=> $row['service_name'],
			'f_image'	=> '<img src="app/images/'.$row['featured_image'].'" style="max-width:70px;" alt="'.$row['featured_image'].'"/>',
			'f_price'   => $row['price'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['type']) && $_GET['type']=='featuredservicedelete')
	{
		$id = $_GET['id'];
		query("Delete FROM `app_slider_services` where id='$id' and branch_id='".$branch_id."'",[],$conn);
		$sql="SELECT * from `app_slider_services` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row){
			$arr[] = array(
			'f_service'	=> $row['service_name'],
			'f_image'	=> '<img src="app/images/'.$row['featured_image'].'" style="max-width:70px;" alt="'.$row['featured_image'].'"/>',
			'f_price'   => $row['price'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['page']) && $_GET['page']=='gallery_images'){
		$sql="SELECT * from `app_gallery` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row)
		{
			$arr[] = array(
			'gallery_images'=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="delete_gallery_images('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['page']) && $_GET['page']=='slider_images'){
		$sql="SELECT * from `app_slider` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row)
		{
			$arr[] = array(
			'slider_images'=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="delete_slider_images('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['page']) && $_GET['page']=='edit_featured_services'){
		$sql="SELECT * from `app_slider_services` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row)
		{
			$arr[] = array(
			'f_service'	=> $row['service_name'],
			'f_image'	=> '<img src="app/images/'.$row['featured_image'].'" style="max-width:70px;" alt="'.$row['featured_image'].'"/>',
			'f_price'   => $row['price'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	
	}else if(isset($_GET['type']) && $_GET['gallery_images_id'] > 0){
		if($_GET['type'] == 'delete'){
			$id = $_GET['gallery_images_id'];
			query("Delete FROM `app_gallery` where id='$id' and branch_id='".$branch_id."'",[],$conn);
			$sql="SELECT * from `app_gallery` where status='1' and branch_id='".$branch_id."'";
			$query=query_by_id($sql,[],$conn);
			foreach($query as $row)
			{
				$arr[] = array(
				'gallery_images'=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
				'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="delete_gallery_images('.$row['id'].',\'delete\');">Delete</a>',
				);
			}	
		}
	}else if(isset($_GET['type']) && $_GET['slider_images_id'] > 0){
		if($_GET['type'] == 'delete'){
			$id = $_GET['slider_images_id'];
			query("Delete FROM `app_slider` where id='$id' and branch_id='".$branch_id."'",[],$conn);
			$sql="SELECT * from `app_slider` where status='1' and branch_id='".$branch_id."'";
			$query=query_by_id($sql,[],$conn);
			foreach($query as $row)
			{
				$arr[] = array(
				'slider_images'=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
				'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="delete_slider_images('.$row['id'].',\'delete\');">Delete</a>',
				);
			}	
		}
	}else if(isset($_GET['page']) && $_GET['page']=='offers'){
		$sql="SELECT * from `app_offer` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row)
		{
			$arr[] = array(
			'offer_name'	=> $row['offer_name'],
			'offer_image'	=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
			'offer_desc'   => $row['offer_desc'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="offer_edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="offer_edit_delete('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['type']) && $_GET['offer_id'] > 0){
		if($_GET['type'] == 'offerdelete'){
			$id = $_GET['offer_id'];
			query("Delete FROM `app_offer` where id='$id' and branch_id='".$branch_id."'",[],$conn);
			$sql="SELECT * from `app_offer` where status='1' and branch_id='".$branch_id."'";
			$query=query_by_id($sql,[],$conn);
			foreach($query as $row)
			{
				$arr[] = array(
					'offer_name'	=> $row['offer_name'],
					'offer_image'	=> '<img src="app/images/'.$row['name'].'" style="max-width:70px;" alt="'.$row['name'].'"/>',
					'offer_desc'   => $row['offer_desc'],
					'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="offer_edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="offer_edit_delete('.$row['id'].',\'delete\');">Delete</a>',
					);
			}	
		}
	}else if(isset($_GET['page']) && $_GET['page']=='aboutus'){
		$sql="SELECT * from `app_aboutus` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row)
		{
			$arr[] = array(
			'about'	=> $row['about_us'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete_about('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete_about('.$row['id'].',\'delete\');">Delete</a>',
			);
		}
	}else if(isset($_GET['type']) && $_GET['edit_about_id']>0){
		if($_GET['type'] == 'aboutusdelete'){
			$id = $_GET['edit_about_id'];
			query("Delete FROM `app_aboutus` where id='$id' and branch_id='".$branch_id."'",[],$conn);
			$sql="SELECT * from `app_aboutus` where status='1' and branch_id='".$branch_id."'";
			$query=query_by_id($sql,[],$conn);
			foreach($query as $row)
			{
				$arr[] = array(
				'about_us'	=> $row['about_us'],
				'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete_about('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete_about('.$row['id'].',\'delete\');">Delete</a>',
				);
			}
		}
	} 
	 
	
	
	

$results = array("sEcho" => 2,
"iTotalRecords" => count($arr),
"iTotalDisplayRecords" => count($arr),
"aaData"=> !empty($arr)?$arr:'');
if(isset($results))
{
	echo json_encode($results);
}
 