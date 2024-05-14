<?php

session_start();
include_once __DIR__ . "/../global_function.php";

class Journal{
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function AddJournal($description){
        try{
            $username = isset($_SESSION["emoney_username"])?$_SESSION["emoney_username"]:'n/a';
            $ip = getIPPublic();

            $query = "INSERT INTO tbl_cp_journal(v_user,v_ip,v_description) VALUES(?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->bindValue(2, $ip, PDO::PARAM_STR);
            $stmt->bindValue(3, $description, PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetJournal($filter_username = '', $filter_date = ''){
        try{
            $query = "SELECT v_user, v_ip, v_description, d_insert FROM tbl_cp_journal WHERE 1=1 ";

            if($filter_username != ''){
                $query .= "AND v_user LIKE '%".$filter_username."%' ";
            }
            if($filter_date != ''){
                $query .= "AND date(d_insert) = date('".$filter_date."')";
            }
            
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
            
        }catch(Exception $e){
            throw $e;
        }
    }
}

?>