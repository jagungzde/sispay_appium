<?php

class Merchant{
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function List($condition = false){
        try{
            if($condition) $query = "SELECT v_merchantcode FROM ms_merchant2 WHERE n_isdeleted = 0 union SELECT 'ALL' ";
            else $query = "SELECT * FROM ms_merchant2 WHERE n_isdeleted = 0 ";

            $stmt = $this->connection->prepare($query);
            // $stmt->bindValue(1, $code, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetMerchant($code){
        try{
            $query = "SELECT * FROM ms_merchant2 WHERE v_merchantcode = ? ";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $code, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;            
        }catch(Exception $e){
            throw $e;
        }
    }

}
