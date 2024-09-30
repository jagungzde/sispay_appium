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
include_once __DIR__ . "/plugins/phpMQTT.php";

$common = new Common();

$processId = $common->GetRandomString(6);

// $logFile = __DIR__ . "/logs/otpsetter_otp_" . date('Y-m-d_H:00:00') . ".txt";
// $common->WriteLog($logFile, 'POST : ' . json_encode($param_POST));

// $phoneNumber = str_replace("\n", "", trim($param_POST['phoneNumber']));
$data = $param_POST['data'];

function MqttPublish($topics, $content)
{
    try {

        $mqtt = new Bluerhinos\phpMQTT(MQTT_HOST, MQTT_PORT, MQTT_CLIENTID);
        if ($mqtt->connect(true, NULL, MQTT_USER, MQTT_PASS)) {
            if (is_array($topics)) {
                foreach ($topics as $topic) {
                    $mqtt->publish($topic, $content, 0, false);
                }
            } else {
                $mqtt->publish($topics, $content, 0, false);
            }

            $mqtt->close();
        } else {
            throw new Exception('MQTT time out');
        }
    } catch (Exception $e) {
        throw $e;
    }
}

try {
    // $conn = new PDO("mysql:host=$MySQL_SERVER;dbname=$MySQL_DB;charset=utf8", $MySQL_USER, $MySQL_PASSWORD);
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbAppium = new DatabaseAppium();
    $connAppium = $dbAppium->GetConnection();

    $db = new Database();
    $conn = $db->GetConnection();

    $token = $common->GetBearerToken();
    // $common->WriteLog($logFile, 'TOKEN : ' . $token);

    $query = "SELECT * FROM ms_login WHERE v_token_otpsetter = ? AND v_active = 'Y'";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) throw new Exception('Invalid Token');

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($row['v_phonenumber'] != $phoneNumber) throw new Exception('Invalid Phone Number');
    $phoneNumber = $row['v_phonenumber'];
    $user = $row['v_user'];


    foreach ($data as $sms) {

        if (strpos($sms['body'], "Never Share Any Code") !== false) {
            $bankCode = 'NAGAD';
        } else if (strpos($sms['body'], "Your bKash verification code is") !== false) {
            $bankCode = 'BKASH';
        } else {
            // $common->WriteLog($logFile, 'Phonenumber : ' . $phoneNumber . ", Id: " . $sms['id'] . " NOT OTP");
            continue;
        }

        // $common->WriteLog($logFile, 'BANK : ' . $bankCode . "");

        $query = "SELECT * FROM tbl_otp WHERE v_phonenumber = ? AND n_smsid = ?";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $phoneNumber, PDO::PARAM_STR);
        $stmt->bindValue(2, $sms['id'], PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // $common->WriteLog($logFile, 'Phonenumber : ' . $phoneNumber . ", User: " . $user . ", Id: " . $sms['id'] . " already exists");
            continue;
        }

        $query = "INSERT INTO tbl_otp (v_phonenumber, n_smsid, v_body, d_insert, n_isused, v_bankcode, v_user) VALUES (?, ?, ?, ?, 0, ?, ?)";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $phoneNumber, PDO::PARAM_STR);
        $stmt->bindValue(2, $sms['id'], PDO::PARAM_STR);
        $stmt->bindValue(3, urlencode($sms['body']), PDO::PARAM_STR);
        $stmt->bindValue(4, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(5, $bankCode, PDO::PARAM_STR);
        $stmt->bindValue(6, $user, PDO::PARAM_STR);
        $stmt->execute();

        // $common->WriteLog($logFile, 'Phonenumber : ' . $phoneNumber . ", User: " . $user . ", Id: " . $sms['id'] . " Saved");

        #region send mqtt
        $statusMqtt = '';
        $topic = "";
        $content = "";
        try {
            // $common->WriteLog($logFile, "   SEND TO APPIUM");


            $content = array(
                "body" => $sms['body'],
            );

            $encodedContent = json_encode($content);
            // $common->WriteLog($logFile, "   CONTENT: " . $encodedContent);
            $content = $encodedContent;

            $tmpphoneNumber = str_replace("+", "", $phoneNumber);
            if ($bankCode == "NAGAD") {
                $topic = "comm-getter-get-otp/" . $tmpphoneNumber;
            } else if ($bankCode == "BKASH") {

                if (substr($tmpphoneNumber, 0, 2) == "88") $tmpphoneNumber = substr($tmpphoneNumber, 2);
                $topic = "send-otp-appium/" . $tmpphoneNumber . "/BKASH";
            }

            // $common->WriteLog($logFile, "   TOPIC: " . $topic);
            // MqttPublish($topic, $encodedContent);
            MqttPublish($topic, '[' . $sms['body'] . ']');
            // $common->WriteLog($logFile, "   SEND TO MQTT SUCCESS");


            $statusMqtt = 'SEND TO MQTT SUCCESS';
        } catch (Exception $ex) {
            // $common->WriteLog($logFile, "   SEND TO MQTT FAILED: " . $ex->getMessage());
            $statusMqtt = 'SEND TO MQTT FAILED: ' . $ex->getMessage();
        }

        $query = "INSERT INTO otp_log (v_otpid, d_insert, v_user, v_content, v_topic, v_status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $sms['id'], PDO::PARAM_STR);
        $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(3, $user, PDO::PARAM_STR);
        $stmt->bindValue(4, urlencode($content), PDO::PARAM_STR);
        $stmt->bindValue(5, $topic, PDO::PARAM_STR);
        $stmt->bindValue(6, $statusMqtt, PDO::PARAM_STR);
        $stmt->execute();
        #endregion
    }

    echo json_encode(array("status" => "success"));
} catch (Exception $e) {
    // $common->WriteLog($logFile, 'ERROR : ' . $e->getMessage());

    echo json_encode(array("status" => "failed", "messages" => $e->getMessage()));
}
