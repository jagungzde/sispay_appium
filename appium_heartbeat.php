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
include_once "class/databaseAppium.php";
include_once "config/base.config.php";
include_once "class/mybank.php";

$common = new Common();
$log = null;
$logDesc = '';
$res = "";
$token = "";
$username = '';
$logFile = "./logs/appium_heartbeat_" . date('Y-m-d_H:00:00') . ".log";

// $phoneNumber = $param_POST['phoneNumber'];

try {

    $db = new Database();
    $conn = $db->GetConnection();

    $dbAppium = new DatabaseAppium();
    $connAppium = $dbAppium->GetConnection();

    $auth = new Auth($conn);
    $heartbeat = new Heartbeat($conn);
    $mybank = new Mybank($conn);
    $log = new Log($conn);

    $state = isset($param_POST['state']) ? $param_POST['state'] : NULL;
    $isWithdraw = isset($param_POST['isWithdraw']) ? $param_POST['isWithdraw'] : 0;
    $isOnline = isset($param_POST['isOnline']) ? $param_POST['isOnline'] : NULL;

    //validate token-----
    $token = $common->GetBearerToken();
    $userData = $auth->GetUserAppium($token);
    $runCode = $common->GetRandomString(6);

    $common->WriteLog($logFile, "[$runCode] ========START========");
    $common->WriteLog($logFile, "[$runCode] PARAMS: " . json_encode($param_POST));
    if ($userData == false) throw new Exception('Invalid Token');
    //------------------------

    $username = $userData['v_username'];
    $mainUser = $userData['v_mainuser'];
    $bankcode = $userData['v_bankcode'];
    $common->WriteLog($logFile, "[$runCode] USERNAME: " . $username);
    $common->WriteLog($logFile, "[$runCode] BANK: " . $bankcode);
    $common->WriteLog($logFile, "[$runCode] STATE: " . $state);
    $common->WriteLog($logFile, "[$runCode] IS WITHDRAW: " . $isWithdraw);
    $common->WriteLog($logFile, "[$runCode] IS ONLINE: " . $isOnline);

    $mainUserData = $auth->GetMainUser($mainUser);

    $emergencyMode = 'off';
    $resMybank = $mybank->GetAccountByUserAndBank($mainUserData['v_phonenumber'], $bankcode);
    foreach ($resMybank as $rowBank) {
        if ($rowBank["v_emergencyMode"]) {
            $emergencyMode = $rowBank["v_emergencyMode"];
            break;
        }
    }
    $updateHb = $heartbeat->UpdateHeartbeatAppium($username, $state, $isOnline);


    if ($state != 'IDLE') {

        $query = "INSERT INTO tbl_login_appium_log (v_user, d_insert, v_state, v_bankcode, n_iswithdraw) VALUES (?,?,?,?,?)";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $mainUser, PDO::PARAM_STR);
        $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(3, $state, PDO::PARAM_STR);
        $stmt->bindValue(4, $bankcode, PDO::PARAM_STR);
        $stmt->bindValue(5, $isWithdraw, PDO::PARAM_STR);
        $stmt->execute();
    }

    $result = array();
    $result['data'] = [
        'emergencyMode' =>  $emergencyMode
    ];
    $common->WriteLog($logFile, "[$runCode] ========END========");
    $res = array("status" => "success", "messages" => $result, "state" => $state);
    // echo json_encode($res); 
} catch (Exception $e) {
    // $common->WriteLog($logFile, 'ERROR : ' . $e->getMessage());
    $common->WriteLog($logFile, "[$runCode] ERROR: " . $e->getMessage());
    $common->WriteLog($logFile, "[$runCode] ========END========");
    $logDesc = "error: " . $e->getMessage() . ", token: " . $token . ($username != "" ? (", username: " . $username) : "");

    if ($log != null)
        $addLog = $log->Add("HEARTBEAT", $logDesc, "AUTOMATION");

    $res = array("status" => "failed", "messages" => $e->getMessage());
}
echo json_encode($res);
