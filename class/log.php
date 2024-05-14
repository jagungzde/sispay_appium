<?php

include_once __DIR__ . '/common.php';

class Log
{
    private $connection = null;

    public function __construct($conn)
    {
        $this->connection = $conn;
    }

    public function Add($event, $log, $system)
    {
        try {

            $common = new Common();
            $id = $common->GUID();

            $query = "INSERT INTO tbl_log VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $event, PDO::PARAM_STR);
            $stmt->bindValue(4, $log, PDO::PARAM_STR);
            $stmt->bindValue(5, $system, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
