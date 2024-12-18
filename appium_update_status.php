<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"));
}

include_once "class/common.php";
include_once "class/database.php";
include_once "class/databaseAppium.php";
include_once "config/base.config.php";

// 0 - offline, 1 - online
$status = !empty($_POST['status']) ? $_POST['status'] : $param_POST->status;
$bank = !empty($_POST['bank']) ? $_POST['bank'] : $param_POST->bank;
$description = !empty($_POST['description']) ? $_POST['description'] : $param_POST->description;

$logFile = "./logs/status_appium_" . date('Y-m-d_H') . ".txt";
$common = new Common();

try {

    $database = new Database();
    $conn = $database->GetConnection();
    $common->WriteLog($logFile, '========START========');

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

    $common->WriteLog($logFile, 'USER: ' . $user);
    $common->WriteLog($logFile, 'PHONENUMBER: ' . $phonenumber);
    $common->WriteLog($logFile, 'BANK: ' . $bank);
    $common->WriteLog($logFile, 'STATUS: ' . $status);
    $common->WriteLog($logFile, 'DESC: ' . $description);

    #region update di sisi login appium
    $query = "UPDATE ms_login_appium SET n_status = ?, v_status_desc = ? WHERE v_username = ? and v_bankcode = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $status, PDO::PARAM_STR);
    $stmt->bindValue(2, $description, PDO::PARAM_STR);
    $stmt->bindValue(3, $user, PDO::PARAM_STR);
    $stmt->bindValue(4, $bank, PDO::PARAM_STR);
    $stmt->execute();
    #endregion

    if (intval($status) == -1) {
        $query = "UPDATE mybank SET v_isactive = 'N' WHERE v_phonenumber = ? and v_bankcode = ? ";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $phonenumber, PDO::PARAM_STR);
        $stmt->bindValue(2, $bank, PDO::PARAM_STR);
        $stmt->execute();

        $query = "SELECT * FROM mybank WHERE v_phonenumber = ? AND v_bankcode = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $phonenumber, PDO::PARAM_STR);
        $stmt->bindValue(2, $bank, PDO::PARAM_STR);
        $stmt->execute();
        $bankAccountNo = '';
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $bankAccountNo = $row['v_bankaccountno'];
        }

        $id = $common->GUID();

        $query = "INSERT INTO tbl_appium_errorlog (v_errorlogid, d_insert, v_user, v_bankaccountno, v_bankcode, v_errordesc, n_issolved) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $id, PDO::PARAM_STR);
        $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(3, $user, PDO::PARAM_STR);
        $stmt->bindValue(4, $bankAccountNo, PDO::PARAM_STR);
        $stmt->bindValue(5, $bank, PDO::PARAM_STR);
        $stmt->bindValue(6, $description, PDO::PARAM_STR);
        $stmt->bindValue(7, "0", PDO::PARAM_STR);
        $stmt->execute();
    }
    #region update di sisi mybank
    // $query = "UPDATE mybank SET n_useAppium = ?, v_appium_status_desc = ? WHERE v_phonenumber = ? and v_bankcode = ? ";
    // $stmt = $conn->prepare($query);
    // $stmt->bindValue(1, $status, PDO::PARAM_STR);
    // $stmt->bindValue(2, $description, PDO::PARAM_STR);
    // $stmt->bindValue(3, $phonenumber, PDO::PARAM_STR);
    // $stmt->bindValue(4, $bank, PDO::PARAM_STR);
    // $stmt->execute();
    #endregion

    $common->WriteLog($logFile, '========END========');

    $res = array("status" => "success", "messages" => "");

    echo json_encode($res);
} catch (Exception $e) {
    $common->WriteLog($logFile, 'ERROR: ' . $e->getMessage());
    $common->WriteLog($logFile, '========END========');
    $res = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($res);
}
