<?php

include_once __DIR__."/../class/merchant.php";
include_once __DIR__."/../class/transaction.php";
include_once __DIR__."/baseCtrl.php";

class TransactionCtrl extends BaseCtrl {

    public function __construct($conn = null){
        parent::__construct($conn);
    }

    /**
     * Resend callback from the transaction
     * 
     * @author rusman
     * @since v3.2.0
     * @param int $futureTrxId -> futuretrxid from transaction
     */
    public function ResendCallback($futureTrxId){
        try{
            if($futureTrxId <= 0) throw new Exception('Invalid Future Trx ID');

            $transactionClass = new Transaction($this->connection);
            $merchantClass = new Merchant($this->connection);

            $transactionData = null;
            $isHistory = false;

            $stmtTrans = $transactionClass->GetTransactionByFutureId($futureTrxId);
            if(count($stmtTrans) == 0){
                $stmtTrans = $transactionClass->GetTransactionHistoryByFutureId($futureTrxId);
                if(count($stmtTrans) > 0){
                    $transactionData = $stmtTrans[0];
                    $isHistory = true;
                }else{
                    throw new Exception('Transaction not found');
                }
            }else{
                $transactionData = $stmtTrans[0];
            }

            $callbackUrl = $transactionData["v_urlfail"];
            if(empty($callbackUrl)) $callbackUrl = $transactionData["v_urlok"];

            $merchantCode = $transactionData["v_merchantcode"];
            $customerCode = $transactionData["v_customercode"];
            $ccy = $transactionData["v_ccy"];
            $amount = $transactionData["n_amount"];
            $transactionId = $transactionData["v_transactionid"];
            $reference = $transactionData["v_reference"];
            $timestamp = $transactionData["d_timestamp"];
            $type = $transactionData["v_transactiontype"];
            $insertDate = $transactionData['d_insert'];
            $bankCode = $transactionData['v_bankcode'];
            $sourceAccountNo = $transactionData['v_accountno'];
            $sourceBankCode = $transactionData['v_sourcebankcode'];
            $statusTransaction = $transactionData['v_status'] == '0' ? 1 : 0;
            $notes2 = $transactionData['v_notes2'];
            $notes3 = $transactionData['v_notes3'];
            if($statusTransaction == 1 && $transactionData['n_isAmountChange'] == 1) $statusTransaction = 2;

            $merchantStmt = $merchantClass->GetMerchant($merchantCode);
            $key = '';
            while($row = $merchantStmt->fetch(PDO::FETCH_ASSOC)){
                $key = $row['v_securecode'];
            }

            $dataParams = array(
                'MerchantCode' => $merchantCode, 
                'CustomerCode' => $customerCode,
                'Currency' => $ccy,
                'BankCode' => $bankCode,
                'Amount' => $amount,
                'LanguageCode' => 'en',
                'TransactionId' => $transactionId,
                'Reference' => $reference,
                'Timestamp' => $timestamp,
                'StatusTransaction' => $statusTransaction,
                'ProcessTime' => $insertDate,
                'Notes2' => $notes2,
                'Notes3' => $notes3,
                'Signature'=> sha1($amount.$bankCode.$ccy.$key.$merchantCode.$customerCode.$timestamp.$statusTransaction)
            );
            
            // writeLog($callbackLogFile, "-----DEPOSIT MANUAL----");
            // writeLog($callbackLogFile, "PARAMS : ".json_encode($dataParams));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $callbackUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataParams));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result= curl_exec($ch);
            
            // writeLog($callbackLogFile, "RESULT : ".json_encode($result));
            if(!$result){
                // writeLog($callbackLogFile, "Error : [".curl_errno($ch)."] ".curl_error($ch));
                $transactionClass->SetCallbackResponse($futureTrxId, "Error : [".curl_errno($ch)."] ".curl_error($ch));
                return "Error : [".curl_errno($ch)."] ".curl_error($ch);
            }else{
                $transactionClass->SetCallbackResponse($futureTrxId, $result);
                return "success";
            }
            curl_close($ch);

        }catch(Exception $e){
            throw $e;
        }
    }

}
