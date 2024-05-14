<?php
// require_once 'config.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"), true);
}

include_once "class/common.php";
include_once "class/database.php";
include_once "class/databaseAppium.php";
include_once "config/base.config.php";

$common = new Common();

$processId = $common->GetRandomString(6);

$logFile = "update_status_transaction_" . date('Y-m-d_H:i:s') . ".txt";
$common->WriteLog($logFile, "[$processid] ===================================");
$common->WriteLog($logFile, "[$processid] POST : " . json_encode($param_POST));

try {

    $dbAppium = new DatabaseAppium();
    $conn = $dbAppium->GetConnection();

    $futureTrxId = $param_POST->futureTrxId;
    $status = $param_POST->status;
    $isMatchSms = $param_POST->isMatchSms;
    $isMatchAppium = $param_POST->isMatchAppium;

    $query = "UPDATE tbl_transaction SET v_status =?, n_ismatchsms = ?, n_ismatchappium =? WHERE n_futuretrxid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $status, PDO::PARAM_STR);
    $stmt->bindValue(2, $isMatchSms, PDO::PARAM_STR);
    $stmt->bindValue(3, $isMatchAppium, PDO::PARAM_STR);
    $stmt->bindValue(4, $futureTrxId, PDO::PARAM_STR);
    $stmt->execute();

    $result = array("status" => "success", "messages" => "");
    echo json_encode($result);

    $common->WriteLog($logFile, "[$processid] DONE ");
} catch (Exception $e) {

    $common->WriteLog($logFile, "[$processid] ERROR: " . $e->getMessage());

    $result = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($result);
}
