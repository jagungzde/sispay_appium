<?php

class Mybank
{
    private $connection = null;

    public function __construct($conn)
    {
        $this->connection = $conn;
    }

    public function GetMybank($accountNo = '')
    {
        try {
            $query = "SELECT * FROM mybank WHERE 1=1 ";

            if ($accountNo != '') {
                $query .= "AND v_bankaccountno = '$accountNo' ";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAccountByBank($bankCode = null)
    {
        if (!isset($bankCode)) throw new Exception("Invalid Bank Code");
        try {

            $query = "SELECT * FROM mybank WHERE v_isactive = 'Y' AND v_bankcode = '$bankCode' ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAccountByUserAndBank($phoneNumber, $bankCode = null)
    {
        if (!isset($phoneNumber)) throw new Exception("Invalid User");
        if (!isset($bankCode)) throw new Exception("Invalid Bank Code");
        try {

            $query = "SELECT * FROM mybank WHERE v_isactive != 'D' AND v_bankcode = '$bankCode' AND v_phonenumber = '$phoneNumber'";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAccountByAccontAndBank($accountNo, $bankCode = null)
    {
        if (!isset($phoneNumber)) throw new Exception("Invalid User");
        if (!isset($bankCode)) throw new Exception("Invalid Bank Code");
        try {

            $query = "SELECT * FROM mybank WHERE v_isactive != 'D' AND v_bankcode = '$bankCode' AND v_bankaccountno = '$accountNo'";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
