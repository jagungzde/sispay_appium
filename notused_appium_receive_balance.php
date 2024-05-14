<?php
// require_once 'config.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"), true);
}

include_once "class/common.php";
include_once "class/database.php";
// include_once "class/databaseAppium.php";
include_once "config/base.config.php";
include_once "controllers/transactionCtrl.php";

$common = new Common();

$processId = $common->GetRandomString(6);

$logFile = __DIR__ . "/logs/receive_balance_" . date('Y-m-d_H:00:00') . ".txt";
$common->WriteLog($logFile, '===================================');
$common->WriteLog($logFile, 'POST : ' . json_encode($param_POST));

try {

    // $conn = new PDO("mysql:host=$MySQL_SERVER;dbname=$MySQL_DB;charset=utf8", $MySQL_USER, $MySQL_PASSWORD);
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $dbAppium = new DatabaseAppium();
    // $connAppium = $dbAppium->GetConnection();

    $db = new Database();
    $conn = $db->GetConnection();

    //validate token-----
    $token = $common->GetBearerToken();
    $common->WriteLog($logFile, 'TOKEN : ' . $token);

    $query = "SELECT A.v_username, B.v_phonenumber FROM ms_login_appium A JOIN ms_login B ON A.v_mainuser = B.v_user WHERE A.v_token = ? AND A.v_system= 'AUTOMATION' ";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) throw new Exception('Invalid Token');

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $phonenumber = $row['v_phonenumber'];
    $user = $row['v_username'];
    //------------------------

    #region update last appium data date dan last balance
    $accountNo = $param_POST['account'];
    $bank = $param_POST['bank'];
    $balance = str_replace(" Tk.", "", str_replace(",", "", $param_POST['balance']));
    $lastAppiumData = date('Y-m-d H:i:s');

    $common->WriteLog($logFile, 'ACCOUNT NO: ' . $accountNo);
    $common->WriteLog($logFile, 'BANK: ' . $bank);
    $common->WriteLog($logFile, 'LAST BALANCE: ' . $balance);
    $common->WriteLog($logFile, 'LAST APPIUM DATE: ' . $lastAppiumData);

    if (is_numeric($balance)) {
        $query = "UPDATE mybank SET n_lastBalance = ? WHERE v_bankaccountno = ? AND v_bankcode = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $balance, PDO::PARAM_STR);
        $stmt->bindValue(2, $accountNo, PDO::PARAM_STR);
        $stmt->bindValue(3, $bank, PDO::PARAM_STR);
        $stmt->execute();
    }
    #endregion

    $result = array("status" => "success", "messages" => "");
    echo json_encode($result);
} catch (Exception $e) {
    $common->WriteLog($logFile, 'ERROR ' . $e->getMessage());
    $result = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($result);
}
