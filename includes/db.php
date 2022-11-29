<?php 
@ini_set('display_errors', 0);

if (get_ip_address() !== get_ip_address(true))
{
    //die("Please wait while we are varifying your details !");
}
// 'username' => 'mammabun_salonbei_salon',
// 'password' => 'salon_786',
// 'database' => 'mammabun_salonsoftware'
$config = array(
	'username' => 'mammabun_salonbei_salon',
	'password' => 'salon_786',
	'database' => 'mammabun_salonsoftware	'  
);



function is_user_logged_in(){
    return isset($_SESSION['user']);
}
function is_admin_logged_in(){
    return isset($_SESSION['admin']);
}
function validate_user_creds($username,$password){
    return ($username === USERNAME && $password ===	PASSWORD);
}
function connect($config){
	try{
		$conn = new PDO("mysql:host=localhost;dbname=".$config['database'],
						$config['username'],
						$config['password']);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		return $conn;
	}catch(Exception $e){
            echo $e->getMessage();die();
		return false;
	}
}
function query($query,$bindings,$conn){
	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);
	try{
		$results = $stmt->FetchAll();
		return $results ? $results : false;
	}catch(Exception $e){
		echo NULL;	
	}
}

function query_str($query,$bindings,$conn){

	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);
	
	return ($stmt->rowCount() > 0)
			? $stmt
			: FALSE ;
	
	
}


function get_insert_id($query,$bindings,$conn){
	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);
	$id = $conn->lastInsertId();
        return $id;
}
function get($tablename, $conn, $vb, $limit = 10){
	try{
		$result = $conn->query("select * from $tablename where vb = $vb ORDER BY id DESC limit $limit");
		return ($result->rowCount() > 0)
			? $result
			: false;
	}catch(Exception $e) {
		return false;	
	}
}
function get_by_id($id,$conn,$vb,$tablename){
	return query("select * from $tablename where id = :id and vb = :vb",
	array(	
		'id'        => $id,
		'vb'        => $vb
	),$conn);
}


function query_by_id($query,$bindings,$conn){
	$stmt = query_str($query,$bindings,$conn);
    if( $stmt ){
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();
	return ( $results )
			? $results
			: false;
    } else {
        return false;
    }	
}

function get_row_count($query,$bindings,$conn){
    $stmt = query_str($query,$bindings,$conn);
    if( $stmt ){
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->rowCount();

            return ( $results )
                            ? $results
                            : false;
    }else{
        return false;
    }
}

function execute_transaction($queries, $bindings, $conn){
    /* Begin a transaction, turning off autocommit */
    $conn->beginTransaction();

    $return_id = NULL;
    
    for( $i = 0; $i < COUNT($queries); $i++ ){
        $stmt = $conn->prepare($queries[$i]);
        $stmt->execute($bindings[$i]);
        if( $i === 0 ){
            $return_id = $conn->lastInsertId();
        }
    }
    try {
        // insert/update query
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
    }
    return $return_id;
}
function get_row_count_new($query,$bindings,$conn){
    try{
		$result = $conn->query($query);
		return ($result->rowCount() > 0)
			? $result->rowCount()
			: false;
	}catch(Exception $e) {
		return false;	
	}
}

function create_table($query, $conn){
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->exec($query);
}