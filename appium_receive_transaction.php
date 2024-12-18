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

$logFile = __DIR__ . "/logs/receive_transaction_" . date('Y-m-d_H:00:00') . ".txt";
$common->WriteLog($logFile, "[$processId] POST : " . json_encode($param_POST));

try {

    // $conn = new PDO("mysql:host=$MySQL_SERVER;dbname=$MySQL_DB;charset=utf8", $MySQL_USER, $MySQL_PASSWORD);
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbAppium = new DatabaseAppium();
    $connAppium = $dbAppium->GetConnection();

    $db = new Database();
    $conn = $db->GetConnection();

    //validate token-----
    $token = $common->GetBearerToken();
    $common->WriteLog($logFile, "[$processId] TOKEN : " . $token);

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
    $lastAppiumDate = date('Y-m-d H:i:s');
    $lastTrxId = $param_POST['lastTrxId'] ?? NULL;

    $common->WriteLog($logFile, '[' . $processId . '] ACCOUNT NO: ' . $accountNo);
    $common->WriteLog($logFile, '[' . $processId . '] BANK: ' . $bank);
    $common->WriteLog($logFile, '[' . $processId . '] LAST BALANCE: ' . $balance);
    $common->WriteLog($logFile, '[' . $processId . '] LAST APPIUM DATE: ' . $lastAppiumDate);
    $common->WriteLog($logFile, '[' . $processId . '] LAST TRX ID: ' . $lastTrxId);

    $query = "UPDATE ms_login_appium SET d_lastcrawler = ?, v_last_trxid = ? WHERE v_token = ?";
    $stmt2 = $conn->prepare($query);
    $stmt2->bindValue(1, $lastAppiumDate, PDO::PARAM_STR);
    $stmt2->bindValue(2, $lastTrxId, PDO::PARAM_STR);
    $stmt2->bindValue(3, $token, PDO::PARAM_STR);
    $stmt2->execute();

    if (is_numeric($balance)) {
        $query = "UPDATE mybank SET n_lastBalance = ? WHERE v_bankaccountno = ? AND v_bankcode = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $balance, PDO::PARAM_STR);
        $stmt->bindValue(2, $accountNo, PDO::PARAM_STR);
        $stmt->bindValue(3, $bank, PDO::PARAM_STR);
        $stmt->execute();
    }
    #endregion

    #region get mybank emergency mode
    $query = "SELECT * FROM mybank WHERE v_bankaccountno = ? AND v_bankcode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $accountNo, PDO::PARAM_STR);
    $stmt->bindValue(2, $bank, PDO::PARAM_STR);
    $stmt->execute();

    $emergencyMode = 'off';
    while ($rowMybank = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $emergencyMode = $rowMybank['v_emergencyMode'];
    }
    #endregion

    $data = isset($param_POST['data']) ? $param_POST['data'] : array();

    $common->WriteLog($logFile, '[' . $processId . '] START LOOP ' . count($data) . " DATA");
    foreach ($data as $row) {

        $common->WriteLog($logFile, '[' . $processId . '] -------------');
        $common->WriteLog($logFile, '[' . $processId . '] DATA : ' . json_encode($row));
        $title = $row['title'];

        if ($title == 'Cash In') continue;

        $amount = str_replace(" Tk.", "", $row['amount']);
        $amount = str_replace(",", "", $amount);

        $trxId = $row['trxId'];
        $account = str_replace("-", "", $row['account']);

        $date = '';
        if ($bank == 'NAGAD') {

            $tmpDate = $row['date'];

            $year = date('Y');

            $arr = explode(", ", $tmpDate);

            $day = date('d');
            $month = date('m');

            if ($arr[0] == "Today") {
                $currentTimestamp = time();
                $year = date('Y', strtotime('-2 hours', $currentTimestamp));
                $month = date('m', strtotime('-2 hours', $currentTimestamp));
                $day = date('d', strtotime('-2 hours', $currentTimestamp));
            } else if ($arr[0] == "Yesterday") {
                $currentTimestamp = time();
                $year = date('Y', strtotime('-26 hours', $currentTimestamp));
                $month = date('m', strtotime('-26 hours', $currentTimestamp));
                $day = date('d', strtotime('-26 hours', $currentTimestamp));
            } else {

                $year = date('Y');
                $arr1 = explode(" ", $arr[0]);

                $day = intval($arr1[1]) < 10 ? '0' . $arr1[1] : $arr1[1];
                $month = "01";

                switch ($arr1[0]) {
                    case "Jan":
                        $month = "01";
                        break;
                    case "Feb":
                        $month = "02";
                        break;
                    case "Mar":
                        $month = "03";
                        break;
                    case "Apr":
                        $month = "04";
                        break;
                    case "May":
                        $month = "05";
                        break;
                    case "Jun":
                        $month = "06";
                        break;
                    case "Jul":
                        $month = "07";
                        break;
                    case "Aug":
                        $month = "08";
                        break;
                    case "Sep":
                        $month = "09";
                        break;
                    case "Oct":
                        $month = "10";
                        break;
                    case "Nov":
                        $month = "11";
                        break;
                    case "Dec":
                        $month = "12";
                        break;
                }
            }


            $tmp2 = explode(" ", $arr[1]);
            $jamminute = $tmp2[0];
            $ampm = $tmp2[1];

            $arr2 = explode(":", $jamminute);

            $jam = $arr2[0];
            $minute = $arr2[1];

            if ($ampm == "AM" && $jam == "12") $jam = "00";
            else if ($ampm == "PM") {
                if (intval($jam) != 12) {
                    $jam = (intval($jam) + 12) . "";
                }
            }

            $jam = intval($jam);
            $jam = $jam < 10 ? '0' . $jam : $jam;


            $date = $year . "-" . $month . "-" . $day . " " . $jam . ":" . $minute . ":00";
        } else if ($bank == 'BKASH') {

            //09:54PM 31/03/2024

            $tmpDate =  $row['date'];
            $arrDate = explode(" ", $tmpDate);

            $indikator = substr($arrDate[0], -2);

            $arrTime = explode(":", substr($arrDate[0], 0, strlen($arrDate[0]) - 2));
            $hour = $arrTime[0];
            $minute = $arrTime[1];

            if ($indikator == "PM") {
                $hour = intval($hour) + 12;
            }

            $time = $hour . ":" . $minute . ":00";

            $arrDay = explode("/", $arrDate[1]);
            $day = $arrDay[0];
            $month = $arrDay[1];
            $year = $arrDay[2];

            $date = $year . "-" . $month . "-" . $day . " " . $time;
        }

        $common->WriteLog($logFile, '[' . $processId . '] DATE : ' . $date);


        $query = "SELECT * FROM appium_transaction WHERE n_amount = ? AND v_trxid = ? AND v_account = ?";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $amount, PDO::PARAM_STR);
        $stmt->bindValue(2, $trxId, PDO::PARAM_STR);
        $stmt->bindValue(3, $account, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $common->WriteLog($logFile, '[' . $processId . '] ALREADY EXISTS ' . $trxId);
            continue;
        }

        $common->WriteLog($logFile, '[' . $processId . '] TIME : ' . time());
        $common->WriteLog($logFile, '[' . $processId . '] APPIUM ID : ' . time() . $trxId);

        $appiumId = time() . $trxId;

        $query = "INSERT INTO appium_transaction (v_id, v_title, n_amount, v_trxid, d_date, v_account, v_user, v_phonenumber, v_agentaccountno, v_bankcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $appiumId, PDO::PARAM_STR);
        $stmt->bindValue(2, $title, PDO::PARAM_STR);
        $stmt->bindValue(3, $amount, PDO::PARAM_STR);
        $stmt->bindValue(4, $trxId, PDO::PARAM_STR);
        $stmt->bindValue(5, $date, PDO::PARAM_STR);
        $stmt->bindValue(6, $account, PDO::PARAM_STR);
        $stmt->bindValue(7, $user, PDO::PARAM_STR);
        $stmt->bindValue(8, $phonenumber, PDO::PARAM_STR);
        $stmt->bindValue(9, $accountNo, PDO::PARAM_STR);
        $stmt->bindValue(10, $bank, PDO::PARAM_STR);
        $stmt->execute();

        if ($emergencyMode != 'off') {
            $common->WriteLog($logFile, '[' . $processId . ']   EMERGENCY MODE: ' . $emergencyMode . ', NOT RUN AUTO MATCHING');
            continue;
        }

        if ($title == 'Cash In') {
            $common->WriteLog($logFile, '   CASH IN TRANSACTION NO NEED TO MATCH');
            continue;
        }

        #region auto matching
        $common->WriteLog($logFile, '[' . $processId . ']   START AUTO MATCHING');
        $query = "SELECT * FROM tbl_transaction WHERE n_amount = ? AND v_notes3 = '$trxId' AND v_accountno = '$account'";

        if ($bank == "BKASH") {

            $first = substr($account, 0, 4);
            $last = substr($account, -3);
            $query = "SELECT * FROM tbl_transaction WHERE n_amount = ? AND v_notes3 = '$trxId' AND v_accountno LIKE '$first%$last'";
        }
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $amount, PDO::PARAM_STR);
        // $stmt->bindValue(2, $trxId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            //tidak ada transaction
            $common->WriteLog($logFile, '[' . $processId . ']   NO TRANSACTION MATCHED');
        } else {
            $rowTrans = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rowTrans['v_status'] != 'T') {
                //bukan order need to check -> abaikan
                $common->WriteLog($logFile, '[' . $processId . ']   STATUS TRANSACTION ' . $rowTrans['v_status'] . ", JUST UPDATE THE APPIUM");

                #region update appium_transaction
                $query = "UPDATE appium_transaction SET n_futuretrxid = ? WHERE v_id = ?";
                $stmt = $connAppium->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->bindValue(2, $appiumId, PDO::PARAM_STR);
                $stmt->execute();
                $common->WriteLog($logFile, '[' . $processId . ']   UPDATE appium_transaction ID: ' . $appiumId);
                #endregion

                #region update transaction
                $dateAppium = date('Y-m-d H:i:s');
                $query = "UPDATE tbl_transaction SET n_ismatchappium = 1, d_matchappiumdate = '$dateAppium' WHERE n_futuretrxid = ?";
                $stmt = $connAppium->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->execute();

                $query = "UPDATE transaction SET n_ismatchappium = 1, d_matchappiumdate = '$dateAppium' WHERE n_futuretrxid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->execute();
                #endregion
            } else {

                $dateAppium = date('Y-m-d H:i:s');

                $matchSms = $rowTrans['n_ismatchsms'] == '1' ? true : false;
                $common->WriteLog($logFile, '[' . $processId . ']   MATCHED WITH ' . $rowTrans['n_futuretrxid']);

                #region update table baru
                $query = "UPDATE tbl_transaction SET n_ismatchappium = '1', d_matchappiumdate = '$dateAppium', v_status = '0', d_completedate = '$dateAppium' ";
                if ($matchSms) {
                    $query .= ", v_memo = 'MATCH SMS AND AUTO MATCH BY AUTO MATCH SERVER' ";
                } else {
                    $query .= ", v_memo = 'NO MATCH SMS AND AUTO MATCH BY AUTO MATCH SERVER' ";
                }
                $query .= " WHERE n_futuretrxid = ?";
                $stmt = $connAppium->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->execute();
                $common->WriteLog($logFile, '[' . $processId . ']   UPDATE tbl_transaction ' . $rowTrans['n_futuretrxid']);
                #endregion

                #region get actual user
                $actualAgent = '';
                $query = "SELECT * FROM ms_login_appium WHERE v_username = ? LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $user, PDO::PARAM_STR);
                $stmt->execute();
                while ($rowAgent = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $actualAgent = $rowAgent['v_mainuser'];
                }
                #endregion

                #region update tbl lama
                $query = "UPDATE `transaction` SET n_ismatchappium = '1', d_matchappiumdate = '$dateAppium', v_status = '0', d_completedate = '$dateAppium' ";
                if ($matchSms) {
                    $query .= ", v_memo = 'MATCH SMS AND AUTO MATCH BY AUTO MATCH SERVER' ";
                } else {
                    $query .= ", v_memo = 'NO MATCH SMS AND AUTO MATCH BY AUTO MATCH SERVER' ";
                }
                $query .= ", v_actual_agent = '$actualAgent' ";
                $query .= " WHERE n_futuretrxid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->execute();
                $common->WriteLog($logFile, '[' . $processId . ']   UPDATE transaction ' . $rowTrans['n_futuretrxid']);
                #endregion

                #region update appium_transaction
                $query = "UPDATE appium_transaction SET n_futuretrxid = ? WHERE v_id = ?";
                $stmt = $connAppium->prepare($query);
                $stmt->bindValue(1, $rowTrans['n_futuretrxid'], PDO::PARAM_STR);
                $stmt->bindValue(2, $appiumId, PDO::PARAM_STR);
                $stmt->execute();
                $common->WriteLog($logFile, '[' . $processId . ']   UPDATE appium_transaction ');
                #endregion

                #region callback
                $common->WriteLog($logFile, '[' . $processId . ']   START CALLBACK ');
                $transCtrl = new TransactionCtrl($conn);
                $callbackResult = $transCtrl->ResendCallback($rowTrans['n_futuretrxid']);
                $common->WriteLog($logFile, '[' . $processId . ']   CALLBACK : ' . $callbackResult);
                #endregion
            }
        }
        #endregion
    }
    $common->WriteLog($logFile, '[' . $processId . ']==========DONE========');
    $result = array("status" => "success", "messages" => "");
    echo json_encode($result);
} catch (Exception $e) {
    $common->WriteLog($logFile, '[' . $processId . '] ERROR ' . $e->getMessage());
    $result = array("status" => "failed", "messages" => $e->getMessage());
    echo json_encode($result);
}
