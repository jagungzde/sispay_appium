<?php

class Transaction
{
    private $connection = null;

    public function __construct($conn)
    {
        $this->connection = $conn;
    }

    public function GetTransactionByFutureId($futureId)
    {
        try {
            $query = "SELECT * FROM `transaction` WHERE n_futuretrxid = ? ";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futureId, PDO::PARAM_STR);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetTransactionHistoryByFutureId($futureId)
    {
        try {
            $query = "SELECT * FROM `transaction_history` WHERE n_futuretrxid = ? ";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futureId, PDO::PARAM_STR);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function AddHistoryBatch($bellowDate)
    {
        try {
            $query = "INSERT INTO `transaction_history` (n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_ccy, v_bankcode, 
                n_amount, d_timestamp, v_clientip, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
                v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid, v_sourcebankcode, 
                v_finalstatus, n_commissionfee, v_resellerid, n_ismanuallybanked, n_isreceivetac, n_isclienttimenull, v_sourceaccountname, 
                v_servername, v_serverurl, n_serverid, v_ifsc, v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
                v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, v_agentassign, v_assignstatus, n_isAmountChange, 
                n_useappium, n_ismatchsms, n_ismatchappium, d_matchsmsdate, d_matchappiumdate ) 
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_ccy, v_bankcode, 
                n_amount, d_timestamp, v_clientip, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
                v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid, v_sourcebankcode, 
                v_finalstatus, n_commissionfee, v_resellerid, n_ismanuallybanked, n_isreceivetac, n_isclienttimenull, v_sourceaccountname, 
                v_servername, v_serverurl, n_serverid, v_ifsc, v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
                v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, v_agentassign, v_assignstatus, n_isAmountChange,
                n_useappium, n_ismatchsms, n_ismatchappium, d_matchsmsdate, d_matchappiumdate  
                FROM `transaction` WHERE d_insert < '$bellowDate'";

            $stmt = $this->connection->prepare($query);
            // $stmt->bindValue(1, $bellowDate, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function AddHistoryBatchOnDate($date)
    {
        try {
            $query = "INSERT INTO `transaction_history` (n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_ccy, v_bankcode, 
                n_amount, d_timestamp, v_clientip, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
                v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid, v_sourcebankcode, 
                v_finalstatus, n_commissionfee, v_resellerid, n_ismanuallybanked, n_isreceivetac, n_isclienttimenull, v_sourceaccountname, 
                v_servername, v_serverurl, n_serverid, v_ifsc, v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
                v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, v_agentassign, v_assignstatus, n_isAmountChange,
                n_useappium, n_ismatchsms, n_ismatchappium, d_matchsmsdate, d_matchappiumdate  ) 
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_ccy, v_bankcode, 
                n_amount, d_timestamp, v_clientip, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
                v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid, v_sourcebankcode, 
                v_finalstatus, n_commissionfee, v_resellerid, n_ismanuallybanked, n_isreceivetac, n_isclienttimenull, v_sourceaccountname, 
                v_servername, v_serverurl, n_serverid, v_ifsc, v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
                v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, v_agentassign, v_assignstatus, n_isAmountChange,
                n_useappium, n_ismatchsms, n_ismatchappium, d_matchsmsdate, d_matchappiumdate   
                FROM `transaction` WHERE date(d_insert) = '$date'";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function DeleteBatch($bellowDate)
    {
        try {
            $query = "DELETE FROM `transaction` WHERE d_insert < ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bellowDate, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function DeleteBatchHistory($bellowDate)
    {
        try {
            $query = "DELETE FROM `transaction_history` WHERE date(d_insert) <= ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bellowDate, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function DeleteBatchOnDate($date)
    {
        try {
            $query = "DELETE FROM `transaction_history` WHERE date(d_insert) = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $date, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function Success($futuretrxid)
    {
        try {
            $query = "UPDATE `transaction` SET v_status = 0 WHERE n_futuretrxid = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SetMerchantResponse($futuretrxid, $response)
    {
        try {
            $query = "UPDATE `transaction` SET v_merchantcallbackresponse = ? WHERE n_futuretrxid = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $response, PDO::PARAM_STR);
            $stmt->bindValue(2, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SuccessHistory($futuretrxid)
    {
        try {
            $query = "UPDATE `transaction_history` SET v_status = 0 WHERE n_futuretrxid = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SetMerchantResponseHistory($futuretrxid, $response)
    {
        try {
            $query = "UPDATE `transaction_history` SET v_merchantcallbackresponse = ? WHERE n_futuretrxid = ?";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $response, PDO::PARAM_STR);
            $stmt->bindValue(2, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWd($startDate, $endDate)
    {
        try {

            $query = "SELECT n_futuretrxid, v_merchantcode, n_amount, 0 as isHistory FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' 
            UNION ALL
            SELECT n_futuretrxid, v_merchantcode, n_amount, 1 as isHistory FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' ";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function GetDepoWd($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('D','W') AND d_insert >= '$startDate' AND d_insert <= '$endDate' 
            UNION ALL
            SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('D','W') AND d_insert >= '$startDate' AND d_insert <= '$endDate' 
            ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('D','W') AND d_insert >= '$startDate' AND d_insert <= '$endDate' AND v_merchantcode = '$merchantCode'
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('D','W') AND d_insert >= '$startDate' AND d_insert <= '$endDate' AND v_merchantcode = '$merchantCode'
                ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepo($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND date(d_insert) = date(d_completedate)
            UNION ALL
            SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND date(d_insert) = date(d_completedate)
            ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
                ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdraw($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND date(d_insert) = date(d_completedate)
            UNION ALL
            SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND date(d_insert) = date(d_completedate)
            ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
                ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAdjustment($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_insert >= '$startDate' AND d_insert < '$endDate'
            UNION ALL
            SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_insert >= '$startDate' AND d_insert < '$endDate'
            ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode'
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_insert >= '$startDate' AND d_insert < '$endDate' AND v_merchantcode = '$merchantCode'
                ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoByCompleteDate($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND date(d_insert) = date(d_completedate)
          UNION ALL
          SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND date(d_insert) = date(d_completedate)
          ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
              UNION ALL
              SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
              ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawByCompleteDate($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND date(d_insert) = date(d_completedate)
          UNION ALL
          SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND date(d_insert) = date(d_completedate)
          ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            UNION ALL
            SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAdjustmentByCompleteDate($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_completedate >= '$startDate' AND d_completedate < '$endDate'
          UNION ALL
          SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_completedate >= '$startDate' AND d_completedate < '$endDate'
          ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode'
              UNION ALL
              SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND d_completedate >= '$startDate' AND d_completedate < '$endDate' AND v_merchantcode = '$merchantCode'
              ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoAdjustment($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
        UNION ALL
        SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
        ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
            UNION ALL
            SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
            ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawAdjustment($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
        UNION ALL
        SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
        ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
            UNION ALL
            SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_insert >= '$startDate' AND th.d_insert < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
            ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoAdjustmentByCompleteDate($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
        UNION ALL
        SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
        ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
            UNION ALL
            SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
            ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawAdjustmentByCompleteDate($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
        UNION ALL
        SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
        ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
            UNION ALL
            SELECT th.n_futuretrxid, th.d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, th.d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE th.d_completedate >= '$startDate' AND th.d_completedate < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
            ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepo_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
        UNION ALL
        SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
        ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            UNION ALL
            SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdraw_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
        UNION ALL
        SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
        ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            UNION ALL
            SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
            ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAdjustment_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate'
        UNION ALL
        SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate'
        ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode'
            UNION ALL
            SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_insert, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode'
            ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoByCompleteDate_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
      UNION ALL
      SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
      ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
          UNION ALL
          SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'D' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
          ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawByCompleteDate_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
      UNION ALL
      SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND date(d_insert) = date(d_completedate)
      ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
          UNION ALL
          SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype = 'W' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode' AND date(d_insert) = date(d_completedate)
          ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetAdjustmentByCompleteDate_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate'
      UNION ALL
      SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate'
      ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate  FROM `transaction` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode'
          UNION ALL
          SELECT n_futuretrxid, DATE_ADD(d_insert, INTERVAL 2 Hour) as d_insert, v_merchantcode, v_customercode, v_bankcode, n_amount, v_transactiontype, v_status, n_fee, v_notes, v_notes2, v_notes3, v_transactionid, v_accountno, v_sourceaccountname, v_dstbankaccountno, v_dstaccountname, v_memo, DATE_ADD(d_completedate, INTERVAL 2 Hour) as d_completedate FROM `transaction_history` WHERE v_status ='0' AND v_transactiontype IN ('Y', 'Z') AND DATE_ADD(d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(d_completedate, INTERVAL 2 Hour) < '$endDate' AND v_merchantcode = '$merchantCode'
          ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoAdjustment_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
      UNION ALL
      SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
      ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
          UNION ALL
          SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
          ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawAdjustment_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
      UNION ALL
      SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
      ORDER BY d_insert ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
          UNION ALL
          SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_insert, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_insert, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
          ORDER BY d_insert ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoAdjustmentByCompleteDate_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
      UNION ALL
      SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
      ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
          UNION ALL
          SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'D'
          ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetWithdrawAdjustmentByCompleteDate_2($startDate, $endDate, $merchantCode = '')
    {
        try {

            $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid  WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
      UNION ALL
      SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
      ORDER BY d_completedate ASC";

            if ($merchantCode != '') {
                $query = "SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate  FROM tbl_transaction_adjustment tta JOIN `transaction` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
          UNION ALL
          SELECT th.n_futuretrxid, DATE_ADD(th.d_insert, INTERVAL 2 Hour) as d_insert, th.v_merchantcode, th.v_customercode, th.v_bankcode, th.n_amount, th.v_transactiontype, th.v_status, th.n_fee, th.v_notes, th.v_notes2, th.v_notes3, th.v_transactionid, th.v_accountno, th.v_sourceaccountname, th.v_dstbankaccountno, th.v_dstaccountname, th.v_memo, DATE_ADD(th.d_completedate, INTERVAL 2 Hour) as d_completedate FROM tbl_transaction_adjustment tta JOIN `transaction_history` th ON tta.n_futuretrxid = th.n_futuretrxid WHERE DATE_ADD(th.d_completedate, INTERVAL 2 Hour) >= '$startDate' AND DATE_ADD(th.d_completedate, INTERVAL 2 Hour) < '$endDate' AND th.v_merchantcode = '$merchantCode' AND tta.n_isdeleted = '0' AND th.v_transactiontype = 'W'
          ORDER BY d_completedate ASC";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateWdFee($futuretrxid, $wdFee, $isHistory = false)
    {
        try {
            if (!$isHistory) {
                $query = "UPDATE `transaction` SET n_fee = ? WHERE n_futuretrxid = ?";
            } else {
                $query = "UPDATE `transaction_history` SET n_fee = ? WHERE n_futuretrxid = ?";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $wdFee, PDO::PARAM_STR);
            $stmt->bindValue(2, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SetCallbackResponse($futureTrxId, $response, $isHistory = false)
    {
        try {
            if (!$isHistory) {
                $query = "UPDATE `transaction` SET v_merchantcallbackresponse = ? WHERE n_futuretrxid = ?";
            } else {
                $query = "UPDATE `transaction_history` SET v_merchantcallbackresponse = ? WHERE n_futuretrxid = ?";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $response, PDO::PARAM_STR);
            $stmt->bindValue(2, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function FirstData()
    {
        try {

            $query = "SELECT * FROM `transaction_history` WHERE v_status = '0' ORDER BY d_insert ASC LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $isGotData = $stmt->rowCount() > 0;
            if (!$isGotData) {
                $query = "SELECT * FROM `transaction` WHERE v_status = '0' ORDER BY d_insert ASC LIMIT 1";
                $stmt = $this->connection->prepare($query);
                $stmt->execute();
                $isGotData = $stmt->rowCount() > 0;
                if ($isGotData) {
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    return null;
                }
            } else {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function Summary($merchantCode, $year, $month, $type = 'D')
    {
        try {

            $query = "SELECT IFNULL(SUM(A.n_amount), 0) as total FROM (
                SELECT n_amount FROM `transaction_history` WHERE v_status = '0' AND v_transactiontype = '$type' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
                UNION ALL
                SELECT n_amount FROM `transaction` WHERE v_status = '0' AND v_transactiontype = '$type' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
            ) A";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryByDateNew($merchantCode, $start, $end, $type = 'D')
    {
        try {
            $query = "SELECT IFNULL(SUM(A.n_amount), 0) as total FROM (
                SELECT n_amount FROM `transaction_history` WHERE v_status = '0' AND v_transactiontype = '$type' 
                AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' 
                AND date(d_insert) = date(d_completedate)
                UNION ALL
                SELECT n_amount FROM `transaction` WHERE v_status = '0' AND v_transactiontype = '$type' 
                AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' 
                AND date(d_insert) = date(d_completedate)
            ) A";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryByDateToday($merchantCode, $start, $end, $type = 'D')
    {
        try {
            $query = "SELECT IFNULL(SUM(n_amount),0) as total FROM `transaction` WHERE v_status = '0' AND v_transactiontype = '$type' 
                AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' 
                AND date(d_insert) = date(d_completedate)";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryByDate($merchantCode, $start, $end, $type = 'D')
    {
        try {
            if ($merchantCode != "ALL") {
                $query = "SELECT IFNULL(SUM(A.n_amount), 0) as total FROM (
                    SELECT n_amount FROM `transaction_history` WHERE v_status = '0' AND v_transactiontype = '$type' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' 
                    UNION ALL
                    SELECT n_amount FROM `transaction` WHERE v_status = '0' AND v_transactiontype = '$type' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end'
                ) A";
            } else {
                $query = "SELECT IFNULL(SUM(A.n_amount), 0) as total FROM (
                    SELECT n_amount FROM `transaction_history` WHERE v_status = '0' AND v_transactiontype = '$type'  AND d_insert >= '$start' AND d_insert <= '$end' 
                    UNION ALL
                    SELECT n_amount FROM `transaction` WHERE v_status = '0' AND v_transactiontype = '$type'  AND d_insert >= '$start' AND d_insert <= '$end'
                ) A";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryFee($merchantCode, $year, $month, $isAgent = false)
    {
        try {
            if ($isAgent) {
                $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                    SELECT n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
                    UNION ALL
                    SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
                ) A";
            } else {
                $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                    SELECT n_fee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
                    UNION ALL
                    SELECT n_fee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND YEAR(d_insert) = $year AND MONTH(d_insert) = $month 
                ) A";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryFeeByDateNew($merchantCode, $start, $end, $isAgent = false)
    {
        try {
            if ($isAgent) {
                $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                    SELECT n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                    UNION ALL
                    SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                ) A";
            } else {
                $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                    SELECT n_fee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                    UNION ALL
                    SELECT n_fee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                ) A";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function SummaryFeeByDateToday($merchantCode, $start, $end, $isAgent = false)
    {
        try {
            if ($isAgent) {
                $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                    SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                ) A";
            } else {
                $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                    SELECT n_fee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' AND date(d_insert) = date(d_completedate)
                ) A";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryFeeByDateAdjustment($merchantCode, $start, $end, $isAgent = false)
    {
        try {
            if ($isAgent) {
                $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                    SELECT n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    AND date(d_insert) != date(d_completedate)
                    UNION ALL
                    SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    AND date(d_insert) != date(d_completedate)
                ) A";
            } else {
                $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                    SELECT n_fee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    AND  date(d_insert) != date(d_completedate)
                    UNION ALL
                    SELECT n_fee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    AND date(d_insert) = date(d_completedate)
                ) A";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SummaryFeeByDate($merchantCode, $start, $end, $isAgent = false)
    {
        try {
            if ($merchantCode != "ALL") {
                if ($isAgent) {
                    $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                        SELECT n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                        UNION ALL
                        SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    ) A";
                } else {
                    $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                        SELECT n_fee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                        UNION ALL
                        SELECT n_fee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert < '$end' 
                    ) A";
                }
            } else {
                if ($isAgent) {
                    $query = "SELECT IFNULL(SUM(A.n_agentCommissionFee),0) as total FROM (
                        SELECT n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0'  AND d_insert >= '$start' AND d_insert < '$end' 
                        UNION ALL
                        SELECT n_agentCommissionFee FROM `transaction` WHERE v_status = '0'  AND d_insert >= '$start' AND d_insert < '$end' 
                    ) A";
                } else {
                    $query = "SELECT IFNULL(SUM(A.n_fee),0) as total FROM (
                        SELECT n_fee FROM `transaction_history` WHERE v_status = '0'  AND d_insert >= '$start' AND d_insert < '$end' 
                        UNION ALL
                        SELECT n_fee FROM `transaction` WHERE v_status = '0' AND d_insert >= '$start' AND d_insert < '$end' 
                    ) A";
                }
            }


            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function CountDepositByAccount($bankcode, $bankaccountno, $checkDate = '')
    {
        try {
            $query = "SELECT IFNULL(count(1),0) as total FROM transaction WHERE v_transactiontype = 'D' AND v_status IN ('0','9') AND v_bankcode = '$bankcode' AND v_dstbankaccountno = '$bankaccountno' ";
            if ($checkDate != '') {
                $query .= " AND d_insert > '$checkDate'";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function TotalDepositByAccount($bankcode, $bankaccountno)
    {
        try {
            $query = "SELECT IFNULL(SUM(n_amount),0) as total FROM transaction WHERE v_dstbankaccountno = '$bankaccountno' AND v_bankcode = '$bankcode' AND v_transactiontype = 'D' AND v_status IN ('0', '9', 'T') AND date(d_insert) = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d'), PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            else return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetForArchive($date)
    {
        try {

            $query = "SELECT * FROM `transaction_history` WHERE date(d_insert) <= '$date'";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetForArchiveOnDate($date)
    {
        try {

            $query = "SELECT * FROM `transaction_history` WHERE date(d_insert) = '$date'";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function InsertHistory($params)
    {
        try {
            $query = "INSERT INTO `transaction_history` (n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_ccy, v_bankcode, 
            n_amount, d_timestamp, v_clientip, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
            v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid, v_sourcebankcode, 
            v_finalstatus, n_commissionfee, v_resellerid, n_ismanuallybanked, n_isreceivetac, n_isclienttimenull, v_sourceaccountname, 
            v_servername, v_serverurl, n_serverid, v_ifsc, v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
            v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, v_agentassign, v_assignstatus,
            n_useappium, n_ismatchsms, n_ismatchappium, d_matchsmsdate, d_matchappiumdate) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $params['n_futuretrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_insert'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_customercode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_ccy'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_bankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_amount'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_timestamp'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_clientip'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactiontype'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_status'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_accountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_fee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactionid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_reference'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlok'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlfail'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstbankaccountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstaccountname'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes2'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_parentfuturetrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_sourcebankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_finalstatus'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_commissionfee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_resellerid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_ismanuallybanked'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isreceivetac'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isclienttimenull'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_sourceaccountname'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_servername'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_serverurl'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_serverid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_ifsc'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes3'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_phonenumber'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcallbackresponse'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_completedate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_memo'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_agentCommissionFee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isMatchMutasi'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isSuccessManually'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_originaldate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_agentassign'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_assignstatus'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_useappium'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_ismatchsms'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_ismatchappium'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_matchsmsdate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_matchappiumdate'], PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateHistory($params)
    {
        try {
            $query = "UPDATE `transaction_history` SET d_insert=?, v_merchantcode=?, v_customercode=?, v_ccy=?, v_bankcode=?, 
            n_amount=?, d_timestamp=?, v_clientip=?, v_transactiontype=?, v_status=?, v_accountno=?, n_fee=?, v_notes=?, v_transactionid=?, 
            v_reference=?, v_urlok=?, v_urlfail=?, v_dstbankaccountno=?, v_dstaccountname=?, v_notes2=?, n_parentfuturetrxid=?, v_sourcebankcode=?, 
            v_finalstatus=?, n_commissionfee=?, v_resellerid=?, n_ismanuallybanked=?, n_isreceivetac=?, n_isclienttimenull=?, v_sourceaccountname=?, 
            v_servername=?, v_serverurl=?, n_serverid=?, v_ifsc=?, v_notes3=?, v_phonenumber=?, v_merchantcallbackresponse=?, d_completedate=?, 
            v_memo=?, n_agentCommissionFee=?, n_isMatchMutasi=?, n_isSuccessManually=?, d_originaldate=?, v_agentassign=?, v_assignstatus=? WHERE n_futuretrxid = ?";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $params['d_insert'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_customercode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_ccy'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_bankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_amount'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_timestamp'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_clientip'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactiontype'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_status'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_accountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_fee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactionid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_reference'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlok'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlfail'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstbankaccountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstaccountname'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes2'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_parentfuturetrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_sourcebankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_finalstatus'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_commissionfee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_resellerid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_ismanuallybanked'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isreceivetac'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isclienttimenull'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_sourceaccountname'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_servername'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_serverurl'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_serverid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_ifsc'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes3'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_phonenumber'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcallbackresponse'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_completedate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_memo'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_agentCommissionFee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isMatchMutasi'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isSuccessManually'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_originaldate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_agentassign'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_assignstatus'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_futuretrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDepoAll()
    {
        try {

            $query = "SELECT *  FROM `transaction` WHERE v_transactiontype = 'D' ORDER BY d_insert ASC";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateTransactionAccName($futureId, $accName)
    {
        try {

            $query = "UPDATE `transaction` SET v_dstaccountname = ? WHERE n_futuretrxid = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $accName, PDO::PARAM_STR);
            $stmt->bindValue(2, $futureId, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetTransactionByAcc($date, $type = 'D', $adj = false, $table = 'transaction')
    {
        try {
            $query = "SELECT date(d_insert) as d_date, v_bankcode, if(v_transactiontype = 'D', v_dstbankaccountno, v_accountno) as v_accno, if(v_transactiontype = 'D', v_dstaccountname, v_sourceaccountname) as v_accname, sum(n_amount) as n_amount 
        from $table where v_status = '0' and v_transactiontype = '{$type}'";

            if (!$adj) {
                $query .= "AND date(d_insert) = '$date' AND date(d_insert) = date(d_completedate)";
            } else {
                $query .= "AND date(d_completedate) = '$date' AND date(d_insert) != date(d_completedate)";
            }
            $query .= "GROUP BY date(d_insert), if(v_transactiontype = 'D', v_dstbankaccountno ,v_accountno ), v_bankcode order by d_insert asc";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    #region transaction adjustment

    public function GetAdjustmentTransactionByAcc($date)
    {
        try {
            $query = "SELECT IFNULL(SUM(IF(v_transactiontype = 'D', n_amount, 0)),0) as deposit, IFNULL(SUM(IF(v_transactiontype = 'W', n_amount, 0)),0) as withdraw, v_accountno, v_bankcode, date(d_insert) as d_date  from tbl_transaction_adjustment where date(d_adjustdate) = '$date' group by date(d_insert), v_accountno, v_bankcode";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Insert data into tbl_transaction_adjustment
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param array params -> transaction data
     * 
     */
    public function AddTransactionAdjustment($params)
    {
        try {

            $query = "INSERT INTO `tbl_transaction_adjustment` (d_adjustdate, n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            //(d_adjustdate, n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee)
            $i = 1;
            $stmt->bindValue($i, !isset($params['adjustDate']) ? date('Y-m-d H:i:s') : $params['adjustDate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_futuretrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_insert'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_customercode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_bankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactiontype'] == 'D' ? $params['v_dstbankaccountno'] : $params['v_accountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactiontype'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_amount'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_fee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_agentCommissionFee'], PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * check if transaction adjustment already exists 
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param int futureTrxId
     * 
     */
    public function IsExistsTransactionAdjustment($futureTrxId)
    {
        try {

            $query = "SELECT * FROM `tbl_transaction_adjustment` WHERE n_futuretrxid = ?";
            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $futureTrxId, PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * flag delete adjustment data
     * 
     * @author rusman
     * @since v3.3.0 - 2022-11-22
     * 
     * @param int futureTrxId
     * 
     */
    public function DeleteAdjustmentTransactionByInsertDate($merchantCode, $date)
    {
        try {

            $query = "UPDATE tbl_transaction_adjustment SET n_isdeleted = 1 WHERE v_merchantcode = ? AND date(d_insert) = ?";
            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * clear adjustment data on date
     * 
     * @author rusman
     * @since v3.3.0 - 2022-11-22
     * 
     * @param int futureTrxId
     * 
     */
    public function ClearAdjustmentTransactionByInsertDate($date)
    {
        try {

            $query = "DELETE FROM tbl_transaction_adjustment WHERE date(d_insert) = ?";
            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $date, PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Update isAdjust, d_adjustDate field in transaction table
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param int futureTrxId 
     * @param bool isHistory 
     * 
     */
    public function SetTransactionAsAdjust($futureTrxId, $isHistory = false)
    {
        try {

            $query = "UPDATE `transaction` SET n_isAdjust = 1, d_adjustDate = ? WHERE n_futuretrxid = ?";
            if ($isHistory) {
                $query = "UPDATE `transaction_history` SET n_isAdjust = 1, d_adjustDate = ? WHERE n_futuretrxid = ?";
            }
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get summary transaction adjustment (transaction that complete != insert)
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string merchantCode 
     * @param string start 
     * @param string end
     * 
     */
    public function GetSummaryAdjustmentTransaction($merchantCode, $start, $end)
    {
        try {
            $query = "
                SELECT date(d_insert) as d_insert, IFNULL(SUM(IF(v_transactiontype = 'D', n_amount, 0)),0) as deposit, IFNULL(SUM(IF(v_transactiontype = 'W', n_amount, 0)),0) as withdraw, IFNULL(SUM(n_fee),0) as n_fee, IFNULL(SUM(n_agentCommissionFee),0) as n_agentCommissionFee FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) GROUP BY date(d_insert)
                UNION ALL
                SELECT date(d_insert) as d_insert, IFNULL(SUM(IF(v_transactiontype = 'D', n_amount, 0)),0) as deposit, IFNULL(SUM(IF(v_transactiontype = 'W', n_amount, 0)),0) as withdraw, IFNULL(SUM(n_fee),0) as n_fee, IFNULL(SUM(n_agentCommissionFee),0) as n_agentCommissionFee FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) GROUP BY date(d_insert)
                ORDER BY d_insert ASC
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list transaction adjustment (transaction that complete != insert)
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string merchantCode 
     * @param string start 
     * @param string end
     * 
     */
    public function GetAdjustmentTransaction($merchantCode, $start, $end)
    {
        try {
            $query = "
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate  FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) 
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) 
                ORDER BY d_insert ASC
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list transaction adjustment (transaction that complete != insert) for specific date
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string merchantCode 
     * @param string start 
     * @param string end
     * 
     */
    public function GetAdjustmentTransactionSpecificDate($merchantCode, $start, $end, $forDate)
    {
        try {
            if ($merchantCode != "ALL") {
                $query = "
                    SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate  FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) AND date(d_insert) = '$forDate'
                    UNION ALL
                    SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) AND date(d_insert) = '$forDate'
                    ORDER BY d_insert ASC
                ";
            } else {
                $query = "
                    SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate  FROM `transaction_history` WHERE v_status = '0' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) AND date(d_insert) = '$forDate'
                    UNION ALL
                    SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate FROM `transaction` WHERE v_status = '0' AND d_completedate >= '$start' AND d_completedate <= '$end' AND date(d_insert) != date(d_completedate) AND date(d_insert) = '$forDate'
                    ORDER BY d_insert ASC
                ";
            }



            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list transaction adjustment by insert date (transaction that complete != insert)
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string merchantCode 
     * @param string start 
     * @param string end
     * 
     */
    public function GetAdjustmentTransactionByInsertDate($merchantCode, $start, $end)
    {
        try {
            $query = "
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate  FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' AND date(d_insert) != date(d_completedate) 
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' AND date(d_insert) != date(d_completedate) 
                ORDER BY d_insert ASC
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetSummaryAdjustmentTransactionByInsertDate($merchantCode, $start, $end)
    {
        try {
            $query = "
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate  FROM `transaction_history` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' AND date(d_insert) != date(d_completedate) 
                UNION ALL
                SELECT n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, v_dstbankaccountno, v_accountno, v_transactiontype, n_amount, n_fee, n_agentCommissionFee, d_completedate FROM `transaction` WHERE v_status = '0' AND v_merchantcode = '$merchantCode' AND d_insert >= '$start' AND d_insert <= '$end' AND date(d_insert) != date(d_completedate) 
                ORDER BY d_insert ASC
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetDuplicateTransactionId($date)
    {
        try {

            $query = "
                SELECT B.* FROM `transaction` B JOIN (
                    SELECT * FROM (SELECT v_transactionid, v_merchantcode, count(n_futuretrxid) as total 
                        FROM `transaction` WHERE date(d_insert) = ? GROUP BY v_transactionid, v_merchantcode) A 
                    WHERE A.total > 1) C ON B.v_transactionid = C.v_transactionid AND B.v_merchantcode = C.v_merchantcode
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $date, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function InsertDuplicateTransIdHistory($params)
    {
        try {
            $query = "INSERT INTO `tbl_duplicate_transid_history` (n_futuretrxid, d_insert, v_merchantcode, v_customercode, v_bankcode, 
            n_amount, d_timestamp, v_transactiontype, v_status, v_accountno, n_fee, v_notes, v_transactionid, 
            v_reference, v_urlok, v_urlfail, v_dstbankaccountno, v_dstaccountname, v_notes2, n_parentfuturetrxid,  
            v_notes3, v_phonenumber, v_merchantcallbackresponse, d_completedate, 
            v_memo, n_agentCommissionFee, n_isMatchMutasi, n_isSuccessManually, d_originaldate, n_isAmountChange) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $params['n_futuretrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_insert'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_customercode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_bankcode'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_amount'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_timestamp'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactiontype'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_status'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_accountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_fee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_transactionid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_reference'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlok'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_urlfail'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstbankaccountno'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_dstaccountname'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes2'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_parentfuturetrxid'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_notes3'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_phonenumber'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_merchantcallbackresponse'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_completedate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['v_memo'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_agentCommissionFee'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isMatchMutasi'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isSuccessManually'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['d_originaldate'], PDO::PARAM_STR);
            $i++;
            $stmt->bindValue($i, $params['n_isAmountChange'], PDO::PARAM_STR);
            $i++;
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetPendingWd($count = 0)
    {
        try {
            $query = "SELECT * FROm `transaction` WHERE v_transactiontype = 'W' AND v_status = 'T' AND v_accountno = '' ORDER By d_insert ASC ";
            if ($count > 0) {
                $query .= " LIMIT " . $count;
            }
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function UpdateAccountNo($futureTrxId, $accountNo, $accountName)
    {
        try {
            $query = "UPDATE `transaction` SET v_accountno = ? , v_sourceaccountname = ? WHERE n_futuretrxid = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $accountNo, PDO::PARAM_STR);
            $stmt->bindValue(2, $accountName, PDO::PARAM_STR);
            $stmt->bindValue(3, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function SendToAppium($futureTrxId)
    {
        try {
            $query = "UPDATE `transaction` SET n_send_to_appium = 1 WHERE n_futuretrxid = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futureTrxId, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    #endregion
}
