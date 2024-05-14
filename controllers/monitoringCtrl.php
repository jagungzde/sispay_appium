<?php

include_once __DIR__."/../class/merchant.php";
include_once __DIR__."/../class/transaction.php";
include_once __DIR__."/../class/sms.php";
include_once __DIR__."/baseCtrl.php";

class MonitoringCtrl extends BaseCtrl {

    public function __construct($conn = null){
        parent::__construct($conn);
    }

    public function CompareSmsWithArchive($date){
        try{

            $sms = new Sms($this->connection);
            $smsArchive = new Sms($this->connectionArchive);

            $listSms = $sms->GetSmsOnDateMatched($date);

            $records = array();
            while($rowSms = $listSms->fetch(PDO::FETCH_ASSOC)){
                $sms = $smsArchive->GetSmsHistoryById($rowSms['v_id']);
                if($sms->rowCount() == 0){
                    array_push($records, $rowSms);
                }
            }
            return $records;

        }catch(Exception $e){
            throw $e;
        }
    }

}