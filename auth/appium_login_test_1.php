<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"));
}

include_once __DIR__ . '/../config/base.config.php';
include_once __DIR__ . '/../class/database.php';
include_once __DIR__ . '/../class/auth.php';
include_once __DIR__ . '/../class/common.php';
include_once __DIR__ . '/../class/log.php';
include_once __DIR__ . '/../class/mybank.php';

$username = !empty($_POST['username']) ? $_POST['username'] : $param_POST->username;
$password = !empty($_POST['password']) ? $_POST['password'] : $param_POST->password;
$serverName = !empty($_POST['serverName']) ? $_POST['serverName'] : $param_POST->serverName;

// $logFile = __DIR__ . "/../logs/login_appium_" . date('Y-m-d_H') . ".txt";

$log = null;
$logDesc = '';
$res = null;

try {
    $database = new Database();
    $conn = $database->GetConnection();

    $auth = new Auth($conn);
    $log = new Log($conn);
    $mybank = new Mybank($conn);
    $common = new Common();

    // $common->WriteLog($logFile, json_encode($param_POST));

    $logDes .= "Username: " . $username . ", Password: " . $password . ", sever name = " . $serverName . "; ";

    $resLogin = $auth->LoginAppium($username, $password);
    if (count($resLogin) == 0) throw new Exception('Invalid Username or Password');

    $res = array("status" => "success", "messages" => "", "data" => array());
    foreach ($resLogin as $row) {

        $token = $common->CreateToken($username);
        $auth->SetTokenAppium($username, $token);
        $auth->SetServerName($serverName, $username);

        $mainUser = $row['v_mainuser'];

        $mainUserData = $auth->GetMainUser($mainUser);
        $resMybank = $mybank->GetAccountByUserAndBank($mainUserData['v_phonenumber'], $row['v_bankcode']);

        foreach ($resMybank as $rowBank) {
            if ($rowBank["v_emergencyMode"]) {
                $emergencyMode = $rowBank["v_emergencyMode"];
                break;
            }
        }

        $bank = $mainUserData['v_bank'];
        $userBank = $mainUserData['v_userbank'];
        $pin = $row['v_bankcode'] == 'BKASH' ? $mainUserData['v_bkash_pin'] : $mainUserData['v_pin'];
        $phoneNumber = $mainUserData['v_phonenumber'];
        $lastcrawl = $row['d_lastcrawler'];
        $lastTrxId = $row['v_last_trxid'];


        $res['data'] = array(
            "username" => $username,
            "token" => $token,
            "phonenumber" => $phoneNumber,
            "bank" => $bank,
            "userBank" => $userBank,
            "pin" => $pin,
            "emergencyMode" => $emergencyMode,
            "lastCrawl" => $lastcrawl,
            "lastTrxId" => $lastTrxId
        );

        $logDes .= "SUCCESS, token: " . $token . "; ";
    }
} catch (Exception $e) {
    $res = array("status" => "failed", "messages" => $e->getMessage(), "data" => array());
    $logDes .= "FAILED, error: " . $e->getMessage() . "; ";
}
$log->Add("LOGIN", $logDes, "AUTOMATION");
echo json_encode($res);
