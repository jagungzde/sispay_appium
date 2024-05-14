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
include_once "class/log.php";

// 0 - failed, 1 - success
$queueId = !empty($_POST['queueId']) ? $_POST['queueId'] : $param_POST->queueId;
$futureTrxId = !empty($_POST['futureTrxId']) ? $_POST['futureTrxId'] : $param_POST->futureTrxId;
$status = !empty($_POST['status']) ? $_POST['status'] : $param_POST->status;
$description = !empty($_POST['description']) ? $_POST['description'] : $param_POST->description;

$logFile = "./logs/appium_callback_wd_" . date('Y-m-d_H') . ".txt";
$runCode = "111111";    //default runcode

try {

    $database = new Database();
    $conn = $database->GetConnection();

    $transactionCtrl = new TransactionCtrl($conn);
    $log = new Log($conn);

    $common = new Common();
    $token = $common->GetBearerToken();
    if ($token == "") throw new Exception('Invalid Token');

    $runCode = $common->GetRandomString(6);

    $common->WriteLog($logFile, "[$runCode] ========START========");

    $common->WriteLog($logFile, "[$runCode] PARAMS: " . json_encode($param_POST));

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

    $common->WriteLog($logFile, "[$runCode] FUTURETRXID: " . $futureTrxId);
    $common->WriteLog($logFile, "[$runCode] STATUS: " . $status);
    $common->WriteLog($logFile, "[$runCode] DESCRIPTION: " . $description);

    $query = "SELECT * FROM `transaction` WHERE n_futuretrxid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $futureTrxId, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) throw new Exception('Future Trx Id not found');

    $wdRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $common->WriteLog($logFile, "[$runCode] CURRENT STATUS: " . $wdRow['v_status']);
    if ($wdRow['v_status'] != 'T') {

        //---update status queue jika status transaksi nya dah berubah
        $query = "UPDATE tbl_agent_wd_queue SET n_isdone = 1, d_donedate = ?, v_statussend = '$description' WHERE v_queueid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(2, $queueId, PDO::PARAM_STR);
        $stmt->execute();
        $next = true;

        throw new Exception('Process FAILED. Future Trx Id status already ' . ($wdRow['v_status'] == '0' ? "success" : "failed"));
    }

    if ($status == 1) {
        $query = "UPDATE `transaction` SET v_status = ?, v_memo = ?, d_completedate = ?, n_useappium = 1 WHERE n_futuretrxid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $status == 1 ? 0 : 1, PDO::PARAM_STR);
        $stmt->bindValue(2, $description, PDO::PARAM_STR);
        $stmt->bindValue(3, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(4, $futureTrxId, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        if($description != "Insufficient balance. Please check and try again later."){
            $query = "UPDATE `transaction` SET v_status = ?, v_memo = ?, d_completedate = ?, n_useappium = 1 WHERE n_futuretrxid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(1, $status == 1 ? 0 : 1, PDO::PARAM_STR);
            $stmt->bindValue(2, $description, PDO::PARAM_STR);
            $stmt->bindValue(3, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(4, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();
        }else{
            $query = "UPDATE `transaction` SET v_memo = ? WHERE n_futuretrxid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(1, $description, PDO::PARAM_STR);
            $stmt->bindValue(2, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    $next = false;
    if ($status == 1) {
        //---update status queue
        $query = "UPDATE tbl_agent_wd_queue SET n_isdone = 1, d_donedate = ?, v_statussend = '$description' WHERE v_queueid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(2, $queueId, PDO::PARAM_STR);
        $stmt->execute();
        $next = true;
    } else {
        $logDesc = "Future Trx Id: $futureTrxId, Agent: $user, Status: Failed, Description: " . $description;
        $addLog = $log->Add("WD CALLBACK", $logDesc, "AUTOMATION");

        //---update status queue
        $query = "UPDATE tbl_agent_wd_queue SET n_isdone = 1, d_donedate = ?, v_statussend = '$description' WHERE v_queueid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(2, $queueId, PDO::PARAM_STR);
        $stmt->execute();
        $next = true;

        #region set agent not used in available account
        /**
         * jika gagal dan deskripsi nya UNKNOWN maka non-active agent nya.
         * 
         * @author Rusman.
         * @since v1.1.0 [2024-02-01] add logic
         */
        if(strtoupper($description) == "UNKNOWN"){
            $query = "SELECT * FROM tbl_agent_wd_queue WHERE v_queueid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(1, $queueId, PDO::PARAM_STR);
            $stmt->execute();

            $rowQueue = $stmt->fetch(PDO::FETCH_ASSOC);

            $query = "UPDATE tbl_available_account_new_wd SET n_isUsed = 0 WHERE v_bankcode = ? AND v_bankaccountno = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(1, $rowQueue['v_bankcode'], PDO::PARAM_STR);
            $stmt->bindValue(2, $rowQueue['v_bankaccountno'], PDO::PARAM_STR);
            $stmt->execute();
        }
        #endregion
    }


    if ($next) {
        //----validate apakah ada assignment sebelum nya. di autoreject dulu
        $query = "SELECT * FROM tbl_agent_assignment WHERE n_futuretrxid = ? AND v_assignstatus = 'P'";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $futureTrxId, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $common->WriteLog($logFile, "[$runCode] GOT AGENT ASSIGNMENT: " . $row['n_assignmentid']);

            if ($status == 1) {
                $common->WriteLog($logFile, "[$runCode] SET AGENT ASSIGNMENT TO A: " . $row['n_assignmentid']);
                $query = "UPDATE tbl_agent_assignment SET v_assignstatus = 'A', v_remarks = 'SUCCESS CRAWLER' WHERE n_assignmentid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $row['n_assignmentid'], PDO::PARAM_STR);
                $stmt->execute();
            } else {
                $common->WriteLog($logFile, "[$runCode] SET AGENT ASSIGNMENT TO R: " . $row['n_assignmentid']);
                $query = "UPDATE tbl_agent_assignment SET v_assignstatus = 'R', v_remarks = 'FAILED CRAWLER: $description' WHERE n_assignmentid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $row['n_assignmentid'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    if ($status == 1) {
        try {
            $merchantCallback = $transactionCtrl->ResendCallback($futureTrxId);
            $common->WriteLog($logFile, "[$runCode] MERCHANT CALLBACK STATUS: " . $merchantCallback);
        } catch (Exception $ex) {
            $common->WriteLog($logFile, "[$runCode] MERCHANT CALLBACK ERROR: " . $ex->getMessage());
        }
    }

    $common->WriteLog($logFile, "[$runCode] ========END========");

    $res = array("status" => "success", "messages" => "");

    echo json_encode($res);
} catch (Exception $e) {
    $common->WriteLog($logFile, "[$runCode] ERROR: " . $e->getMessage());
    $common->WriteLog($logFile, "[$runCode] ========END========");
    $res = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($res);
}
