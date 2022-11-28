<?php 
define ('SITE_ROOT', realpath(dirname(__FILE__)));

function get_session_auth(){
    return encryptIt(SITE_ROOT);
}

function view($path, $data = NULL){
	if( $data ){
		extract( $data );
	}	
        
        $script = 'includes/js/'. $path . '.js.php';

	$path = $path . '.view.php';
        
	include "views/layout.php";
}


function admin_view($path, $data = NULL){
	if( $data ){
		extract( $data );
	}	

	$path = $path . '.view.php';
	
	include "../views/admin/layout.php";
}


function alert($message,$style){
    return 
        "<div class='alert alert-".$style." alert-dismissible' role='alert'>
            <div class='alert-box'>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>		
                        <span aria-hidden='true'>&times;</span>
                </button>"
                ."<span class='alert-msg'>"
                . $message 
                ."</span>"
            ."</div>"
        . "</div>";			
}


function old($key){
    if(!empty($_POST[$key])){
            return htmlspecialchars($_POST[$key]);
    }
    return NULL;
}
function empty_fields($client_data){
    $flag = FALSE;
    foreach($client_data as $info){
            if(empty($info)){
                    $flag = TRUE;
            }
    }
    return $flag;
	//return ($flag == 0) ? true : false;	
}
function encryptIt( $q ) {
    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q,MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}
	
function decryptIt( $q ) {
    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

function format_d_m_y($date){
    $new_date = NULL;
    if( strpos($date, '-') ){
        
        $old_date = explode('-', $date);
        
        $new_date =  isset( $old_date[2] ) ?  $old_date[2] : '00';
        $new_date .= '-';
        $new_date .= isset( $old_date[1] ) ?  $old_date[1] : '00';
        $new_date .= '-';
        $new_date .= $old_date[0];       
        
    }elseif( strpos($date, '/') ){
        
        $old_date = explode('/', $date);
        
        $new_date =  isset( $old_date[2] ) ?  $old_date[2] : '00';
        $new_date .= '-';
        $new_date .= isset( $old_date[1] ) ?  $old_date[1] : '00';
        $new_date .= '-';
        $new_date .= $old_date[0]; 
    }
    return $new_date;
}

function check_login_creds($name, $pass, $conn ){
    $result =  salon\DB\get_by_id("SELECT user_id,user_name,user_type FROM login_info "
                . " WHERE (user_name = :name OR user_contact = :name ) "
                . " AND user_password = :pass", [
                    'name' => $name,
                    'pass' =>$pass
                ], $conn)[0];
    
    if( $result ){
        $user_id = $result['user_id'];
        $user_type = $result['user_type'];
        $params = [
            'name' => $result['user_name'],
            'user_id' => $user_id,
        ];
        ( (int)$user_type === 1 ) ? set_admin_session($params) : set_employee_session($params);
        return TRUE;
    }else{
        $result = \salon\DB\get_by_id("SELECT e.employee_id, employee_name FROM employees e "
                . " INNER JOIN employee_login_info l WHERE ( l.user_name = :username OR e.phone = :username ) "
                . " AND l.password = :pass LIMIT 1", 
                [
                    'username' =>$name,
                    'pass' => encryptIt( $pass )
                ], $conn)[0];
        
        if( $result ){
            $user_id = $result['employee_id'];
            $params = [
                'name' => $result['employee_name'],
                'user_id' => $user_id,
            ];
            set_employee_session($params);

            return TRUE;
        }
    }
    return FALSE;
}

function validate_login_creds($name, $pass){
    if( !empty($name) && !empty($pass) ){
        return TRUE;
    }
    return FALSE;
}

function set_admin_session($info){
    extract($info);
    
    $_SESSION['user_name'] = $name;
    $_SESSION['type'] = 'admin';
    $_SESSION['login_id'] = $user_id;
    $_SESSION['AUTH_ID'] = get_session_auth();
}

function set_employee_session($info){
    extract($info);
    
    $_SESSION['user_name'] = $name;
    $_SESSION['type'] = 'employee';
    $_SESSION['login_id'] = $user_id;
    $_SESSION['AUTH_ID'] = get_session_auth();
}

function logout_user(){
    unset( $_SESSION['user_name'] );
    unset( $_SESSION['type'] );
    unset( $_SESSION['login_id'] );
    unset( $_SESSION['AUTH_ID'] );
}


function check_image($fileToUpload){
    return getimagesize($fileToUpload["tmp_name"]);
}

function check_file_size($fileToUpload,$size){
    if ($fileToUpload["size"] > $size) {
        return FALSE;
    }
    return TRUE;
}

function check_file_type($imageFileType){
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return FALSE;
    }
    return TRUE;
}

function create_folder($target_dir){
    mkdir($target_dir, 0777, true);
}

function check_file_exist($name,$extension){
    $increment = ''; //start with no suffix

    while(file_exists($name . $increment . '.' . $extension)) {
        $increment++;
    }
    return $name . $increment . '.' . $extension;
}

function upload_image($fileToUpload ){
    
    $size_limits = array(
        '1mb' => '125000',
        '2mb' => '250000',
        '3mb' => '375000',
        '4mb' => '500000',
        '5mb' => '625000',
    );
    
    $size = $size_limits['1mb'];
    /**
     * get name and extension of the file to 
     * check if there is a file already existing in the directory 
     * we rename it and then upload 
     */
    $name = pathinfo($fileToUpload['name'], PATHINFO_FILENAME);
    $extension = pathinfo($fileToUpload['name'], PATHINFO_EXTENSION);
    
    // Check if file already exists
    $file_name = check_file_exist($name, $extension);
    
    $target_dir = "uploads/";
    
    $dir_to_save = "../uploads/";
    
    /**
     *  we will check if the targeted 
     *  directory does not exist we will create a new one
     */
    if (!file_exists($target_dir)) {
        create_folder($target_dir);
    }
    
    $target_file = $target_dir . $file_name;
    
    $target_to_move = $dir_to_save . $file_name;
    
    $imageFileType = strtolower( pathinfo($target_file,PATHINFO_EXTENSION) );
    
    $response = [];
    $response[0] = TRUE;
    // Check if image file is a actual image or fake image
    $check = check_image($fileToUpload);
    
    if($check === FALSE) {
        $response[0] = FALSE;
        $response[1] = "File is not an Image";
        return $response;
    } 
     // Check file size
    $check2 = check_file_size($fileToUpload, $size);
    
    if( $check2 === FALSE ){
        $response[0] = FALSE;
        $response[1] = "File Size is Too Large";
        return $response;
    }
    // Allow certain file formats
    $check3 = check_file_type($imageFileType);
    
    if( $check3 === FALSE ){
        $response[0] = FALSE;
        $response[1] = "Only jpeg, gif, png, jpeg Image Formats are Allowed To upload <br/> Your file format is " .$imageFileType;
        return $response;
    }
    
    if (move_uploaded_file($fileToUpload["tmp_name"], $target_to_move)) {
        $response[0] = TRUE;
        $response[1] = $target_file;
        return $response;
    } else {
        $response[0] = FALSE;
        $response[1] = "Error Occured While Upload The File, Please Try Again..";
        return $response;
    }
}

/**
 * Billing Functions
 */

function product_selling_price( $product_id, $conn){
    return salon\DB\get_by_id("SELECT selling_price as sp FROM inventory "
            . " WHERE id = :id "
            . " LIMIT 1",
            [
                'id' => $product_id,
            ], $conn)[0]['sp'];
}

function discount_amount( $discount_id, $conn){
    return salon\DB\get_by_id("SELECT amount FROM discounts WHERE discount_id = :id LIMIT 1", [
        'id' => $discount_id
    ], $conn)[0]['amount'];
}

function product_name( $product_id, $conn ){
    return salon\DB\get_by_id("SELECT p.name as name FROM products p "
            . " INNER JOIN inventory i "
            . " ON i.product_id = p.id "
            . " WHERE i.id = :id ",
            [
                'id' => $product_id
            ], $conn)[0]['name'];
}

function available_qty( $product_id , $conn ){
    return salon\DB\get_by_id("SELECT ( stock - stock_sold ) as qty "
            . " FROM inventory i "
            . " WHERE i.id = :id ",
            [
                'id' => $product_id
            ], $conn)[0]['qty'];
}

function service_cost($service_id, $conn){
    return (float)\salon\DB\get_by_id("SELECT price FROM services WHERE sid = :id LIMIT 1", [
        'id' => $service_id,
    ], $conn)[0]['price'];
}

function service_name( $service_id, $conn ){
    return \salon\DB\get_by_id("SELECT service_name as name FROM services WHERE sid = :id LIMIT 1", [
        'id' => $service_id
    ], $conn)[0]['name'];
}

function tax_percent( $tax_id, $conn ){
    return \salon\DB\get_by_id("SELECT charges FROM taxes WHERE id = :id LIMIT 1",
            [
                'id' => $tax_id,
            ], $conn)[0]['charges'];
}

function service_commission( $beautician_id, $conn ){
    return (float)\salon\DB\get_by_id("SELECT service_commission FROM beauticians WHERE beautician_id = :id LIMIT 1",
            [
                'id' => $beautician_id,
            ], $conn)[0]['service_commission'];
}

function staff_name($employee_id, $conn){
    return \salon\DB\get_by_id("SELECT employee_name as name FROM employees WHERE employee_id = :id", [
        'id' => $employee_id,
    ], $conn)[0]['name'];
}

function payment_status($status){
    switch((int)$status){
        case 1:
            return 'Paid';
        
        case 2: 
            return 'Pending';
    }
}

function appointment_status($status){
    switch((int)$status){
        case 1:
            return 'New';
        
        case 2: 
            return 'Pending';
            
        case 2: 
            return 'Cancel';
    }
}

function company_name( $conn ){
    return \salon\DB\get_by_id("SELECT CONCAT( company_name , ' ', shop ) AS name FROM company_profile WHERE id = 1 ", [], $conn)[0]['name'];
}

