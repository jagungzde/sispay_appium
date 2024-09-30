<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"));
}

include_once "class/common.php";
include_once "class/database.php";
// include_once "class/databaseAppium.php";
include_once "config/base.config.php";
include_once "controllers/transactionCtrl.php";

$logFile = "./logs/appium_receive_wd_" . date('Y-m-d_H') . ".txt";
$runCode = "111111";    //default runcode

$queueId = $param_POST->queueId;

$common = new Common();
try {

    $database = new Database();
    $conn = $database->GetConnection();

    $transactionCtrl = new TransactionCtrl($conn);

    $token = $common->GetBearerToken();
    if ($token == "") throw new Exception('Invalid Token');

    $runCode = $common->GetRandomString(6);

    $common->WriteLog($logFile, "[$runCode] ========START========");

    $query = "SELECT A.v_mainuser, A.v_username, B.v_phonenumber FROM ms_login_appium A JOIN ms_login B ON A.v_mainuser = B.v_user WHERE A.v_token = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $token, PDO::PARAM_STR);
    $stmt->execute();

    $phonenumber = '';
    $user = '';
    $mainUser = '';
    if ($stmt->rowCount() == 0) throw new Exception('Invalid Token');
    else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = $row['v_username'];
            $phonenumber = $row['v_phonenumber'];
            $mainUser = $row['v_mainuser'];
        }
    }

    $common->WriteLog($logFile, "[$runCode] USERNAME: " . $user);
    $common->WriteLog($logFile, "[$runCode] PHONENUMBER: " . $phonenumber);
    $common->WriteLog($logFile, "[$runCode] MAIN USER: " . $mainUser);
    $common->WriteLog($logFile, "[$runCode] QUEUE ID: " . $queueId);

    $query = "UPDATE `tbl_agent_wd_queue` SET n_isreceived = 1, d_receiveddate = ? WHERE v_queueid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt->bindValue(2, $queueId, PDO::PARAM_STR);
    $stmt->execute();


    $common->WriteLog($logFile, "[$runCode] ========END========");

    $res = array("status" => "success", "messages" => "");

    echo json_encode($res);
} catch (Exception $e) {
    $common->WriteLog($logFile, "[$runCode] ERROR: " . $e->getMessage());
    $common->WriteLog($logFile, "[$runCode] ========END========");
    $res = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($res);
}
