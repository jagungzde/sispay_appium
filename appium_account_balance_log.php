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
include_once "controllers/transactionCtrl.php";

$common = new Common();

$processId = $common->GetRandomString(6);

$logFile = __DIR__ . "/logs/account_balance_log_" . date('Y-m-d_H:00:00') . ".txt";
$common->WriteLog($logFile, 'POST : ' . json_encode($param_POST));

try {

    $dbAppium = new DatabaseAppium();
    $connAppium = $dbAppium->GetConnection();

    $db = new Database();
    $conn = $db->GetConnection();

    $common = new Common();

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

    $accountNo = $param_POST['accountNo'];
    $bank = $param_POST['bank'];
    $insertDate = date('Y-m-d H:i:s');

    $timestamp = $param_POST['timestamp'];

    $timestampDatetime = date('Y-m-d H:i:s');

    if ($bank == "NAGAD") {
        $timestampDatetime = $common->ConvertAppiumDateIntoDatetime($timestamp);
    } else if ($bank == "BKASH") {
        $timestampDatetime = $common->ConvertAppiumDateIntoDatetimeBkash($timestamp);
    }


    $query = "INSERT INTO tbl_account_balance_log (v_user, d_timestamp, d_insert, v_bankaccountno, v_bankcode, 
        n_startingBalance, n_currentBalance, n_cashOut, n_coCommission, n_coTransactions, n_cashIn, n_ciCommission, n_ciTransactions,
        n_b2bReceive, n_brTransactions, n_b2bSend, n_bsTransactions) VALUES (?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $user, PDO::PARAM_STR);
    $stmt->bindValue(2, $timestampDatetime, PDO::PARAM_STR);
    $stmt->bindValue(3, $insertDate, PDO::PARAM_STR);
    $stmt->bindValue(4, $accountNo, PDO::PARAM_STR);
    $stmt->bindValue(5, $bank, PDO::PARAM_STR);
    $stmt->bindValue(6, $param_POST['startingBalance'], PDO::PARAM_STR);
    $stmt->bindValue(7, $param_POST['currentBalance'], PDO::PARAM_STR);
    $stmt->bindValue(8, $param_POST['cashOut'], PDO::PARAM_STR);
    $stmt->bindValue(9, $param_POST['coCommission'], PDO::PARAM_STR);
    $stmt->bindValue(10, $param_POST['coTransactions'], PDO::PARAM_STR);
    $stmt->bindValue(11, $param_POST['cashIn'], PDO::PARAM_STR);
    $stmt->bindValue(12, $param_POST['ciCommission'], PDO::PARAM_STR);
    $stmt->bindValue(13, $param_POST['ciTransactions'], PDO::PARAM_STR);
    $stmt->bindValue(14, $param_POST['b2bReceive'], PDO::PARAM_STR);
    $stmt->bindValue(15, $param_POST['brTransactions'], PDO::PARAM_STR);
    $stmt->bindValue(16, $param_POST['b2bSend'], PDO::PARAM_STR);
    $stmt->bindValue(17, $param_POST['bsTransactions'], PDO::PARAM_STR);
    $stmt->execute();

    $result = array("status" => "success", "messages" => "");
    echo json_encode($result);
} catch (Exception $e) {
    $common->WriteLog($logFile, 'ERROR ' . $e->getMessage());
    $result = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($result);
}
