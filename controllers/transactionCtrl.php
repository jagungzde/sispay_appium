<?php

include_once __DIR__ . "/../config/base.config.php";
include_once __DIR__ . "/../class/merchant.php";
include_once __DIR__ . "/../class/transaction.php";
include_once __DIR__ . "/../class/setting.php";
include_once __DIR__ . "/../class/mybank_new.php";
include_once __DIR__ . "/../class/agent.php";
// include_once __DIR__ . "/../class/user.php";
include_once __DIR__ . "/baseCtrl.php";

class TransactionCtrl extends BaseCtrl
{

    public function __construct($conn = null)
    {
        parent::__construct($conn);
    }

    /**
     * Resend callback from the transaction
     * 
     * @author rusman
     * @since v3.2.0
     * @param int $futureTrxId -> futuretrxid from transaction
     */
    public function ResendCallback($futureTrxId, $logFile = '')
    {

        try {
            if ($futureTrxId <= 0) throw new Exception('Invalid Future Trx ID');

            $callbackLogFile = __DIR__ . "/../logs/controller-transaction-resendcallback-" . date("Ymd") . ".log";

            $transactionClass = new Transaction($this->connection);
            $merchantClass = new Merchant($this->connection);
            $agentClass = new Agent($this->connection);
            $mybankClass = new Mybank($this->connection);

            $transactionData = null;
            $isHistory = false;

            $stmtTrans = $transactionClass->GetTransactionByFutureId($futureTrxId);
            if (count($stmtTrans) == 0) {
                $stmtTrans = $transactionClass->GetTransactionHistoryByFutureId($futureTrxId);
                if (count($stmtTrans) > 0) {
                    $transactionData = $stmtTrans[0];
                    $isHistory = true;
                } else {
                    throw new Exception('Transaction not found');
                }
            } else {
                $transactionData = $stmtTrans[0];
            }

            $callbackUrl = $transactionData["v_urlfail"];
            if (empty($callbackUrl)) $callbackUrl = $transactionData["v_urlok"];

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
            $v_phonenumber = $transactionData['v_phonenumber'];
            $memo = $transactionData['v_memo'];
            $fee = $transactionData['n_fee'];

            $agentPhonenumber = '';
            $pin = '';
            if ($merchantCode == 'C01BDT') {
                if ($type == 'D') {
                    $agentStmt = $agentClass->GetAgentByUsername($transactionData['v_actual_agent']);
                    while ($row = $agentStmt->fetch(PDO::FETCH_ASSOC)) {
                        $agentPhonenumber = $row['v_phonenumber'];
                    }
                } else {
                    $mybankStmt = $mybankClass->GetMybank($sourceAccountNo, $bankCode);
                    while ($row = $mybankStmt->fetch(PDO::FETCH_ASSOC)) {
                        $agentPhonenumber = $row['v_phonenumber'];
                    }
                }

                if (strlen($agentPhonenumber) > 0)
                    $pin = substr($agentPhonenumber, -4);
            }

            if (isset($transactionData['n_isAmountChange']))
                if ($statusTransaction == 1 && $transactionData['n_isAmountChange'] == 1) $statusTransaction = 2;

            $merchantStmt = $merchantClass->GetMerchant($merchantCode);
            $key = '';
            while ($row = $merchantStmt->fetch(PDO::FETCH_ASSOC)) {
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
                'Memo' => $memo,
                'Notes2' => $notes2,
                'Notes3' => $notes3,
                'SourceAccountNo' => $sourceAccountNo,
                'PhoneNumber' => $v_phonenumber,
                'Fee' => $fee,
                'Signature' => sha1($amount . $bankCode . $ccy . $key . $merchantCode . $customerCode . $timestamp . $statusTransaction)
            );

            if ($type == 'W') {
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
                    'Memo' => $memo,
                    'Notes2' => $notes2,
                    'Notes3' => $notes3,
                    'Fee' => $fee,
                    'Signature' => sha1($amount . $bankCode . $ccy . $key . $merchantCode . $customerCode . $timestamp . $statusTransaction)
                );
            }

            if ($merchantCode == 'C01BDT') {
                $dataParams['PIN'] = $pin;
            }

            if ($logFile != '') {
                $this->writeLog($logFile, "-----START CALLBACK----");
                $this->writeLog($logFile, "PARAMS : " . json_encode($dataParams));
                $this->writeLog($logFile, "URL : " . $callbackUrl);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $callbackUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataParams));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);

            if ($logFile != '')
                $this->writeLog($logFile, "RESULT : " . json_encode($result));

            if (!$result) {
                if ($logFile != '')
                    $this->writeLog($logFile, "Error : [" . curl_errno($ch) . "] " . curl_error($ch));

                $transactionClass->SetCallbackResponse($futureTrxId, "Error : [" . curl_errno($ch) . "] " . curl_error($ch));
                return "Error : [" . curl_errno($ch) . "] " . curl_error($ch);
            } else {
                if ($logFile != '')
                    $this->writeLog($logFile, "Success");

                $transactionClass->SetCallbackResponse($futureTrxId, $result);
                return "success";
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get destination account no for transaction deposit
     * 
     * @author rusman
     * @since v6.9.0 - 2023-12-06
     *
     * @param int $accountSelectionMethod 
     * @param string $bankCode
     * @param string $merchantCode
     * @param float $amount
     * @return string destination account no
     */
    public function GetDestinationAccount($accountSelectionMethod, $bankCode, $merchantCode, $amount)
    {
        try {

            $settingClass = new Setting($this->connection);
            $transactionClass = new Transaction($this->connection);
            $mybankNewClass = new Mybank($this->connection);

            $destinationAccNo = '';
            if ($accountSelectionMethod == 0) {
                //RANDOM
                $destinationAccNo = $transactionClass->DepositSelectionRandom($amount, $merchantCode, $bankCode);
            } else if ($accountSelectionMethod == 1) {
                //ROUND ROBIN
                $destinationAccNo = $transactionClass->DepositSelectionRoundRobin($amount, $merchantCode, $bankCode);
            } else if ($accountSelectionMethod == 3) {
                //PRE-AVAILABLE LIST NEW
                $availableDate = '';
                $settingData = $settingClass->GetSetting('LAST_AVAILABLE_ACCOUNT_TIME_NEW');
                if (count($settingData) > 0) {
                    $availableDate = $settingData[0]['v_value'];
                }
                if ($availableDate == '') throw new Exception('Available Account New not found');

                $agentAssignment = false;
                $settingData = $settingClass->GetSetting('ENABLE_AGENT_ASSIGNMENT');
                if (count($settingData) > 0) {
                    $agentAssignment = $settingData[0]['v_value'] == 'Y' ? true : false;
                }

                $groupId = 1;

                $destinationAccNo = $transactionClass->DepositSelectionPreListNew($amount, $merchantCode, $bankCode, $availableDate, $agentAssignment, $groupId);
                if ($destinationAccNo == '') {
                    $groupId = 2;
                    $destinationAccNo = $transactionClass->DepositSelectionPreListNew($amount, $merchantCode, $bankCode, $availableDate, $agentAssignment, $groupId);

                    if ($destinationAccNo == '') {
                        $groupId = 3;
                        $destinationAccNo = $transactionClass->DepositSelectionPreListNew($amount, $merchantCode, $bankCode, $availableDate, $agentAssignment, $groupId);
                    }
                }

                if ($destinationAccNo != '') {
                    try {
                        $mybankNewClass->UpdateCounter($bankCode, $destinationAccNo, $merchantCode, $availableDate);
                    } catch (Exception $ex) {
                    }
                }
            }

            return $destinationAccNo;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate if customer is scammer or not
     * 
     * @author rusman
     * @since v6.11.1 - 2024-05-16
     *
     * @param string $customerCode customer code.
     * @param string $logFile log file path.
     * @return bool status scam.
     */
    public function IsScamCustomer($customerCode, $logFile = "", $runCode = "")
    {
        try {
            if ($logFile != "")
                $this->writeLog($logFile, "[$runCode] -----START VALIDATE SCAM CUSTOMER----");
            if ($logFile != "")
                $this->writeLog($logFile, "[$runCode] Customer: $customerCode");

            $date = date('Y-m-d H:i:s', strtotime('-12 Hours'));

            if ($logFile != "")
                $this->writeLog($logFile, "[$runCode] Last 12 Hours: $date");


            $query = "SELECT * FROM `transaction` WHERE v_status = 'T' AND v_customercode = ? AND d_insert >= '$date'";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $customerCode, PDO::PARAM_STR);
            $stmt->execute();

            if ($logFile != "")
                $this->writeLog($logFile, "[$runCode] Transaction (T) Last 12 Hour: " . $stmt->rowCount());

            if ($stmt->rowCount() > 10) return true;
            else return false;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Calculate Merchant Fee
     * 
     * @author rusman
     * @since v6.12.0 - 2024-07-18
     *
     * @param string $merchantCode merchant code.
     * @param string $type transaction type [D/W].
     * @param string $amount transaction amount.
     * @return number new merchant fee.
     */
    public function CalculateMerchantFee($merchantCode, $type, $amount)
    {
        try {

            $merchantClass = new Merchant($this->connection);

            $merchantStmt = $merchantClass->GetMerchant($merchantCode);
            $merchantInfo = $merchantStmt->fetch(PDO::FETCH_ASSOC);

            $newFee = 0;
            if ($type == 'D') {
                if ($merchantInfo['v_customerdepositfeetype'] == 'P') {
                    $newFee = round($merchantInfo['n_customerdepositfeevalue'] / 100 * $amount, 2);
                } else {
                    $newFee = $merchantInfo['n_customerdepositfeevalue'];
                }
            } else {
                if ($merchantInfo['v_customerwithdrawfeetype'] == 'P') {
                    $newFee = round($merchantInfo['n_customerwithdrawfeevalue'] / 100 * $amount, 2);
                } else {
                    $newFee = $merchantInfo['n_customerwithdrawfeevalue'];
                }
            }

            return $newFee;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate Agent Fee by Bank Account
     * 
     * @author rusman
     * @since v6.12.0 - 2024-07-18
     *
     * @param string $accountNo agent bank account no.
     * @param string $bankCode bank code.
     * @param string $type transaction type [D/W].
     * @param string $amount transaction amount.
     * @return number new agent fee.
     */
    public function CalculateAgentFee($accountNo, $bankCode, $type, $amount)
    {
        try {

            $mybankClass = new Mybank($this->connection);

            $mybankArr = $mybankClass->GetMybank($accountNo, $bankCode);
            $mybankInfo = $mybankArr[0];

            $newFee = 0;
            if ($type == 'D') {

                $newFee = round($mybankInfo['n_agentCommission'] / 100 * $amount, 2);
            } else {

                $newFee = round($mybankInfo['n_agentCommissionWithdraw'] / 100 * $amount, 2);
            }

            return $newFee;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
