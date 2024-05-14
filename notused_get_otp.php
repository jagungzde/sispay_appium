<?php
	// require_once 'config.php';
	header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    if(stripos($_SERVER["CONTENT_TYPE"],"application/json")==0)
    {
        $param_POST = json_decode(file_get_contents("php://input"), true);
    }

    include_once "class/common.php";
    include_once "class/database.php";
    include_once "class/databaseAppium.php";
    include_once "config/base.config.php";

    $common = new Common();

    $processId = $common->GetRandomString(6);

    $logFile = "GETOTP_".date('Y-m-d_H:00:00').".txt";
	// writeLog($logFile, 'POST : '.json_encode($param_POST));

    //{"phoneNumber":"+15555215554","data":[{"id":"26","body":"Test Kirim2 lagi 6"},{"id":"25","body":"Test Kirim2 lagi 5"},{"id":"24","body":"Test Kirim2 lagi 4"},{"id":"23","body":"Test Kirim2 lagi 3"},{"id":"22","body":"Test Kirim2 lagi 2"}]}

    $phoneNumber = $param_POST['phoneNumber'];
    
    try{
        // $conn = new PDO("mysql:host=$MySQL_SERVER;dbname=$MySQL_DB;charset=utf8", $MySQL_USER, $MySQL_PASSWORD);
        // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $dbAppium = new DatabaseAppium();
        $connAppium = $dbAppium->GetConnection();

        $db = new Database();
        $conn = $db->GetConnection();

        //validate token-----
        $token = $common->GetBearerToken();

        $query = "SELECT * FROM ms_login WHERE v_appium_token_getter = ? AND v_active = 'Y'";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $token, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() == 0) throw new Exception('Invalid Token');

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['v_phonenumber'] != $phoneNumber) throw new Exception('Invalid Phone Number');
        //------------------------


        $query = "SELECT * FROM tbl_otp WHERE v_phonenumber = ? AND n_isused = 0 ORDER BY n_smsid DESC, d_insert DESC LIMIT 1";
        $stmt = $connAppium->prepare($query);
        $stmt->bindValue(1, $phoneNumber, PDO::PARAM_STR);
        $stmt->execute();

        $body = '';
        $id = 0;
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $body = urldecode($row['v_body']);
            $id = $row['n_smsid'];
        }

        if($id != 0){
            $query = "UPDATE tbl_otp SET n_isused = 1 WHERE v_phonenumber = ? AND n_smsid = ?";
            $stmt = $connAppium->prepare($query);
            $stmt->bindValue(1, $phoneNumber, PDO::PARAM_STR);
            $stmt->bindValue(2, $id, PDO::PARAM_STR);
            $stmt->execute();
        }
    
        echo json_encode(array("status" => "success", "data" => array("body" => urldecode($body))));
    }catch(Exception $e){
        $common->WriteLog($logFile, 'ERROR : '.$e->getMessage());

        echo json_encode(array("status" => "failed", "messages" => $e->getMessage()));
    }
