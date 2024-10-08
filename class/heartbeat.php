<?php

include_once __DIR__ . '/common.php';

class Heartbeat
{
    private $connection = null;

    public function __construct($conn)
    {
        $this->connection = $conn;
    }

    public function UpdateHeartbeatAppium($username, $state = '', $isOnline = '')
    {
        try {

            $query = "UPDATE ms_login_appium SET d_heartbeat = ?, v_state = ?, n_isonline = ? WHERE v_username = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $state, PDO::PARAM_STR);
            $stmt->bindValue(3, $isOnline, PDO::PARAM_STR);
            $stmt->bindValue(4, $username, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateHeartbeatOtpSetter($username)
    {
        try {

            $query = "UPDATE ms_login SET d_heartbeat_otpsetter = ? WHERE v_user = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateHeartbeatComGetter($username)
    {
        try {

            $query = "UPDATE ms_login_appium SET d_heartbeat = ? WHERE v_username = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateHeartbeatWithdraw($username)
    {
        try {
            $query = "UPDATE ms_login_appium set d_heartbeatWithdraw = ? WHERE v_username = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
