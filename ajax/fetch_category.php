<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['catID']) && $_GET['catID'] > 0){
		$catID = $_GET['catID'];
		query("update `servicecat` set active=1 where id=$catID",[],$conn);
	}
	## Read value
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	
	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (cat like '%".$searchValue."%' ) ";
	}
 
	 
	
	$sql="SELECT count(*) as allcount from servicecat where active=0 ".$sql_in.$searchQuery;
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT count(*) as allcount from servicecat where active=0 ".$searchQuery,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	
	
	$empQuery = "SELECT * from servicecat where active=0 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	
	
	$empRecords = query_by_id($empQuery,[],$conn);
	$data = array();
	foreach($empRecords as $row) {
		 
		 
				$data[] = array( 
				"cat"=>$row['cat'],
				"action"=>'<a onclick="deleteCat('.$row['id'].')"><button class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</button></a>',
				);
			}
	 
		
		## Response
		$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $totalRecordwithFilter,
		"iTotalDisplayRecords" => $totalRecords,
		"aaData" => $data
		);
		
		echo json_encode($response);
		