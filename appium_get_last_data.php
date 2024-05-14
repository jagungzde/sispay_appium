<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"), true);
}

include_once "class/common.php";
include_once "class/database.php";
include_once "class/databaseAppium.php";
include_once "config/base.config.php";

$accountNo = !empty($_POST['account']) ? $_POST['account'] : $param_POST['account'];
$bank = !empty($_POST['bank']) ? $_POST['bank'] : $param_POST['bank'];

// $logFile = "./logs/get_last_appium_data_" . date('Y-m-d_H') . ".txt";

try {

    $database = new Database();
    $conn = $database->GetConnection();

    $databaseAppium = new DatabaseAppium();
    $connAppium = $databaseAppium->GetConnection();

    $common = new Common();
    // $common->WriteLog($logFile, '========START========');

    $token = $common->GetBearerToken();
    if ($token == "") throw new Exception('Invalid Token');

    $query = "SELECT A.v_username, B.v_phonenumber FROM ms_login_appium A JOIN ms_login B ON A.v_mainuser = B.v_user WHERE A.v_token = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $token, PDO::PARAM_STR);
    $stmt->execute();

    $phonenumber = '';
    $user = '';
    if ($stmt->rowCount() == 0) throw new Exception('Invalid Token');
    else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = $row['v_username'];
            $phonenumber = $row['v_phonenumber'];
        }
    }

    $query = "SELECT * FROM appium_transaction WHERE v_agentaccountno = ? and v_bankcode = ? ORDER BY d_date DESC LIMIT 1";
    $stmt = $connAppium->prepare($query);
    $stmt->bindValue(1, $accountNo, PDO::PARAM_STR);
    $stmt->bindValue(2, $bank, PDO::PARAM_STR);
    $stmt->execute();

    $data = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, $row);
    }

    // $common->WriteLog($logFile, '========END========');

    $res = array("status" => "success", "messages" => "", "data" => $data);

    echo json_encode($res);
} catch (Exception $e) {
    // $common->WriteLog($logFile, 'ERROR: ' . $e->getMessage());
    // $common->WriteLog($logFile, '========END========');
    $res = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($res);
}
