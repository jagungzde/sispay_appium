<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (stripos($_SERVER["CONTENT_TYPE"], "application/json") == 0) {
    $param_POST = json_decode(file_get_contents("php://input"), true);
}

include_once "class/database.php";

include_once "class/common.php";
include_once "class/log.php";
include_once "class/auth.php";
include_once "class/heartbeat.php";
include_once "config/base.config.php";
include_once "class/mybank.php";

$futuretrxid = $param_POST['futuretrxid'];
$result = '';

try {
    $db = new Database();
    $conn = $db->GetConnection();

    $querySelect = "SELECT v_queueid FROM tbl_agent_wd_queue WHERE n_futuretrxid = ? ORDER BY d_insert DESC LIMIT 1";
    $stmt = $conn->prepare($querySelect);
    $stmt->bindParam(1, $futuretrxid, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !isset($row['v_queueid'])) {
        throw new Exception("No record found for the provided futuretrxid");
    }

    $v_queueid = $row['v_queueid'];

    $queryUpdate = "UPDATE tbl_agent_wd_queue SET d_processtime = ? WHERE v_queueid = ?";
    $stmt = $conn->prepare($queryUpdate);
    $currentTime = date('Y-m-d H:i:s');
    $stmt->bindParam(1, $currentTime, PDO::PARAM_STR);
    $stmt->bindParam(2, $v_queueid, PDO::PARAM_STR);
    $stmt->execute();

    $result = [
        "status" => "ok",
        "message" => "",
        "records" => []
    ];
} catch (Exception $e) {
    $result = [
        "status" => "no",
        "message" => $e->getMessage(),
        "records" => [],
    ];
}

echo json_encode($result);

$conn = null;
