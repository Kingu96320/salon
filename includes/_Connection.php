<?php

class SqlUtil {

    static $db_user = "root";
    static $db_password = "";
    static $db_database = "yb";
    static $db_host = "localhost";

    function __construct() {
        
    }

    static function getConnection() {
        try {
            $host = SqlUtil::$db_host;
            $database = SqlUtil::$db_database;
            $con = new PDO("mysql:host={$host };dbname={$database}", SqlUtil::$db_user, SqlUtil::$db_password);
        } catch (PDOException $exception) { //to handle connection error
            echo "Connection error: " . $exception->getMessage();
        }
        return $con;
    }

    static function getData($query, $param = null) {
        $con = SqlUtil::getConnection();
        $stmt = $con->prepare($query);
        if (($param != null) && (count($param) > 0)) {

            for ($i = 0; $i < count($param); $i++) {
                $stmt->bindValue($i + 1, $param[$i]);
            
            }
        }
        $stmt->execute();

        return $stmt;
    }

    static function ins_up_del($query, $param = null) {
        $con = SqlUtil::getConnection();
        $stmt = $con->prepare($query);
        if (($param != null) && (count($param) > 0)) {

            for ($i = 0; $i < count($param); $i++) {
                $stmt->bindValue($i + 1, $param[$i]);
            }
        }
        $x = $stmt->execute();

        $id = $con->lastInsertId();
        return $id;
    }

}

?>
