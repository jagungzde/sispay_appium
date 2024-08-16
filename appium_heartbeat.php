<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"), true);
}

include_once "class/common.php";
include_once "class/log.php";
include_once "class/auth.php";
include_once "class/heartbeat.php";
include_once "class/database.php";
include_once "config/base.config.php";
include_once "class/mybank.php";

$common = new Common();
$log = null;
$logDesc = '';
$res = null;
$token = "";
$username = '';
// $logFile = "GETCOMMAND_" . date('Y-m-d_H:00:00') . ".txt";
// $common->WriteLog($logFile, 'POST : ' . json_encode($param_POST));

// $phoneNumber = $param_POST['phoneNumber'];

try {

    $db = new Database();
    $conn = $db->GetConnection();

    $auth = new Auth($conn);
    $heartbeat = new Heartbeat($conn);
    $mybank = new Mybank($conn);
    $log = new Log($conn);

    //validate token-----
    $token = $common->GetBearerToken();

    $userData = $auth->GetUserAppium($token);
    if ($userData == false) throw new Exception('Invalid Token');
    //------------------------

    $username = $userData['v_username'];
    $mainUser = $userData['v_mainuser'];
    $bankcode = $userData['v_bankcode'];

    $mainUserData = $auth->GetMainUser($mainUser);

    $resMybank = $mybank->GetAccountByUserAndBank($mainUserData['v_phonenumber'], $bankcode);
    $emergencyMode = $resMybank["v_emergencyMode"];

    $updateHb = $heartbeat->UpdateHeartbeatAppium($username);

    $result['data'] = [
        'emergencyMode' =>  $emergencyMode
    ];

    $res = array("status" => "success", "messages" => $result);
} catch (Exception $e) {
    // $common->WriteLog($logFile, 'ERROR : ' . $e->getMessage());
    $logDesc = "error: " . $e->getMessage() . ", token: " . $token . ($username != "" ? (", username: " . $username) : "");

    $addLog = $log->Add("HEARTBEAT", $logDesc, "AUTOMATION");

    $res = array("status" => "failed", "messages" => $e->getMessage());
}

echo json_encode($res);
