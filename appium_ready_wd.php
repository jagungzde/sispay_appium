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

$logFile = "./logs/appium_ready_wd_" . date('Y-m-d_H') . ".txt";
$runCode = "111111";    //default runcode

try {

    $database = new Database();
    $conn = $database->GetConnection();

    $transactionCtrl = new TransactionCtrl($conn);

    $common = new Common();
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

    $query = "SELECT * FROM `tbl_agent_wd_queue` WHERE v_user = ? 
        AND n_isdone = 0
        ORDER BY d_insert ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $mainUser, PDO::PARAM_STR);
    $stmt->execute();

    while ($rowQueue = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $common->WriteLog($logFile, "[$runCode] NEXT QUEUE ID: " . $rowQueue['v_queueid']);
        $common->WriteLog($logFile, "[$runCode] NEXT QUEUE FUTURETRXID: " . $rowQueue['n_futuretrxid']);

        $query = "SELECT * FROM `transaction` WHERE n_futuretrxid = ?";
        $stmtTrans = $conn->prepare($query);
        $stmtTrans->bindValue(1, $rowQueue['n_futuretrxid'], PDO::PARAM_STR);
        $stmtTrans->execute();

        $rowTrans = $stmtTrans->fetch(PDO::FETCH_ASSOC);

        $common->WriteLog($logFile, "[$runCode] CURRENT STATUS: " . $rowTrans['v_status']);
        if ($rowTrans['v_status'] != 'T') {
            $common->WriteLog($logFile, "[$runCode] BECAUSE TRANSACTION ALREADY PROCESS, GO TO NEXT QUEUE");


            $query = "UPDATE tbl_agent_wd_queue SET n_isdone = 1, v_statussend = 'DONE BECAUSE TRANSACTION STATUS IS PROCESS' WHERE v_queueid = ?";
            $stmtUp = $conn->prepare($query);
            $stmtUp->bindValue(1, $rowQueue['v_queueid'], PDO::PARAM_STR);
            $stmtUp->execute();

            continue;
        }

        //prepare
        $futureTrxId = $rowQueue['n_futuretrxid'];
        $sourceAccNo = $rowQueue['v_bankaccountno'];
        $sourceAccName = $rowQueue['v_bankaccountname'];
        $dstAccNo = $rowTrans['v_dstbankaccountno'];
        $dstAccName = $rowTrans['v_dstaccountname'];
        $amount = $rowTrans['n_amount'];
        $bankCode = $rowTrans['v_bankcode'];

        $status = '';

        try {
            $common->WriteLog($logFile, "[$runCode]   SEND TO AUTOMATION");

            $content = array(
                "queueId" => $rowQueue['v_queueid'],
                "futureTrxId" => $futureTrxId,
                "sourceAccountNo" => $sourceAccNo,
                "sourceAccountName" => $sourceAccName,
                "destAccountNo" => $dstAccNo,
                "destAccountName" => $dstAccName,
                "amount" => $amount,
                "bank" => $bankCode
            );

            $encodedContent = json_encode($content);
            $common->WriteLog($logFile, "[$runCode]   CONTENT: " . $encodedContent);

            $topic = "send-wd-appium/" . $sourceAccNo;

            $common->WriteLog($logFile, "[$runCode]   TOPIC: " . $topic);

            MqttPublish($topic, $encodedContent);

            $status = "SEND TO AUTOMATION SUCCESS";
            $flag = 1;

            $common->WriteLog($logFile, "[$runCode]   SEND TO AUTOMATION SUCCESS");
        } catch (Exception $ex) {
            $common->WriteLog($logFile, "[$runCode]   SEND TO AUTOMATION FAILED: " . $ex->getMessage());
            $status = "SEND TO AUTOMATION FAILED: " . $ex->getMessage();
            $flag = 0;
        }

        $query = "UPDATE tbl_agent_wd_queue SET n_issendtomqtt = $flag, d_senddate = '" . date('Y-m-d H:i:s') . "', v_statussend = '$status' WHERE v_queueid = ?";
        $stmtUp = $conn->prepare($query);
        $stmtUp->bindValue(1, $rowQueue['v_queueid'], PDO::PARAM_STR);
        $stmtUp->execute();

        break;
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
