<?php

class Sms{
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function AddHistory($params){
        try{
            $query = "INSERT INTO tbl_sms_history (v_id, v_user, d_timestamp, v_message, v_sender, v_sn, d_read, 
                v_type, v_securitycode, v_phonenumber, n_futuretrxid, v_customerphone, n_amount) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $params['v_id'], PDO::PARAM_STR);
            $stmt->bindValue(2, $params['v_user'], PDO::PARAM_STR);
            $stmt->bindValue(3, $params['d_timestamp'], PDO::PARAM_STR);
            $stmt->bindValue(4, $params['v_message'], PDO::PARAM_STR);
            $stmt->bindValue(5, $params['v_sender'], PDO::PARAM_STR);
            $stmt->bindValue(6, $params['v_sn'], PDO::PARAM_STR);
            $stmt->bindValue(7, $params['d_read'], PDO::PARAM_STR);
            $stmt->bindValue(8, $params['v_type'], PDO::PARAM_STR);
            $stmt->bindValue(9, $params['v_securitycode'], PDO::PARAM_STR);
            $stmt->bindValue(10, $params['v_phonenumber'], PDO::PARAM_STR);
            $stmt->bindValue(11, $params['n_futuretrxid'], PDO::PARAM_STR);
            $stmt->bindValue(12, $params['v_customerphone'], PDO::PARAM_STR);
            $stmt->bindValue(13, $params['n_amount'], PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function AddHistoryBatch($bellowDate){
        try{
            $query = "INSERT INTO tbl_sms_history (v_id, v_user, d_timestamp, v_message, v_sender, v_sn, d_read, v_type, v_securitycode, v_phonenumber, n_futuretrxid, v_customerphone, n_amount) SELECT v_id, v_user, d_timestamp, v_message, v_sender, v_sn, d_read, v_type, v_securitycode, v_phonenumber, n_futuretrxid, v_customerphone, n_amount FROM tbl_sms WHERE d_timestamp < ?";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bellowDate, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function DeleteBatch($bellowDate){
        try{
            $query = "DELETE FROM tbl_sms WHERE d_timestamp < ?";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bellowDate, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }
}

?>