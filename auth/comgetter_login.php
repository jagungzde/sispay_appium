<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"));
}

include_once __DIR__ . '/../config/base.config.php';
include_once __DIR__ . '/../class/database.php';
include_once __DIR__ . '/../class/databaseDevel.php';
include_once __DIR__ . '/../class/auth.php';
include_once __DIR__ . '/../class/common.php';
include_once __DIR__ . '/../class/log.php';

$username = !empty($_POST['username']) ? $_POST['username'] : $param_POST->username;
$password = !empty($_POST['password']) ? $_POST['password'] : $param_POST->password;
// $bank = !empty($_POST['bank']) ? $_POST['bank'] : $param_POST->bank;

$logFile = __DIR__ . "/../logs/login_comgetter_" . date('Y-m-d_H') . ".txt";

$log = null;
$logDesc = '';
$res = null;

try {

    $database = new Database();
    $conn = $database->GetConnection();

    $databaseDevel = new DatabaseDevel();
    $connDevel = $databaseDevel->GetConnection();

    $auth = new Auth($conn);
    $log = new Log($conn);
    $common = new Common();

    // $common->WriteLog($logFile, json_encode($param_POST));

    $logDes .= "Username: " . $username . ", Password: " . $password . "; ";

    $resLogin = $auth->LoginComGetter($username, $password, $bank);
    if (count($resLogin) == 0) throw new Exception('Invalid Username or Password');

    $res = array("status" => "success", "messages" => "", "data" => array());
    foreach ($resLogin as $row) {

        $token = $common->CreateToken($username);
        $auth->SetTokenComGetter($username, $token);

        $mainUser = $row['v_mainuser'];

        $mainUserData = $auth->GetMainUser($mainUser);
        $bank = $mainUserData['v_bank'];
        $userBank = $mainUserData['v_userbank'];
        $pin = $mainUserData['v_pin'];
        $phoneNumber = $mainUserData['v_phonenumber'];

        $res['data'] = array("username" => $username, "token" => $token, "phonenumber" => $phoneNumber);

        $logDes .= "SUCCESS, token: " . $token . "; ";
    }
} catch (Exception $e) {
    $res = array("status" => "failed", "messages" => $e->getMessage(), "data" => array());
    $logDes .= "FAILED, error: " . $e->getMessage() . "; ";
}
$log->Add("LOGIN", $logDes, "COM GETTER");
echo json_encode($res);
