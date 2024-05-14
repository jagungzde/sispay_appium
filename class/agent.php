<?php
//include_once __DIR__ . "/../global_function.php";

class Agent{
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function GetAgentByPhonenumber($phone){
        try{
            $query = "SELECT * FROM ms_login WHERE v_phonenumber = ? ";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $phone, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetAgentByToken($token){
        try{
            $query = "SELECT * FROM ms_login WHERE v_agent_token = ? ";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $token, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * cash out -> deposit
     */
    public function GetTotalCashOutByDate($from, $to, $agentPhone, $bankcode = ''){
        try{
            $query = "SELECT 
                    IFNULL(SUM(A.n_amount),0) as TotalAmount, IFNULL(COUNT(A.n_futuretrxid),0) as TotalCount, IFNULL(SUM(IFNULL(A.n_agentCommissionFee,0)),0) as TotalCommission
                FROM `transaction` A JOIN mybank B ON A.v_dstbankaccountno = B.v_bankaccountno AND A.v_bankcode = B.v_bankcode 
                WHERE A.v_status = '0' AND A.v_transactiontype = 'D' AND B.v_phonenumber = ? AND A.d_insert >= '$from' AND A.d_insert <= '$to' ";

            if($bankcode != ''){
                $query .= " AND A.v_bankcode = '$bankcode'";
            }
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $agentPhone, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * cash in -> wd
     */
    public function GetTotalCashInByDate($from, $to, $agentPhone, $bankcode = ''){
        try{
            $query = "SELECT 
                    IFNULL(SUM(A.n_amount),0) as TotalAmount, IFNULL(COUNT(A.n_futuretrxid),0) as  TotalCount, IFNULL(SUM(IFNULL(A.n_agentCommissionFee,0)),0) as TotalCommission
                FROM `transaction` A JOIN mybank B ON A.v_accountno = B.v_bankaccountno AND A.v_bankcode = B.v_bankcode 
                WHERE A.v_status = '0' AND A.v_transactiontype = 'W' AND B.v_phonenumber = ? AND A.d_insert >= '$from' AND A.d_insert <= '$to' ";
            if($bankcode != ''){
                $query .= " AND A.v_bankcode = '$bankcode'";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $agentPhone, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetWdRequest($filter, $agentUser){
        try{
            $query = "SELECT A.*,  B.v_transactionid, B.v_dstbankaccountno, B.d_timestamp, B.v_notes3, B.v_bankcode, B.n_amount
                FROM `tbl_agent_assignment` A JOIN `transaction` B ON A.n_futuretrxid = B.n_futuretrxid 
                WHERE A.v_user = ? AND (B.v_transactionid LIKE '%$filter%' OR B.v_phonenumber LIKE '%$filter%') AND (DATE(A.d_assigndate) = ? OR A.v_assignstatus = 'P') ORDER BY A.d_assigndate DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $agentUser, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d'), PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveResponse($id, $status, $file, $remarks){
        try{

            $query = "UPDATE tbl_agent_assignment SET v_assignstatus = ?, v_file = ?, v_remarks = ?  WHERE n_assignmentid = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $status, PDO::PARAM_STR);
            $stmt->bindValue(2, $file, PDO::PARAM_STR);
            $stmt->bindValue(3, $remarks, PDO::PARAM_STR);
            $stmt->bindValue(4, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function UpdateTransaction($futuretrxid, $status){
        try{
            if($status == 'A'){
                $query = "UPDATE `transaction` SET v_assignstatus = ?, v_status = '0' WHERE n_futuretrxid = ?";
            }else{
                $query = "UPDATE `transaction` SET v_assignstatus = ?  WHERE n_futuretrxid = ?";
            }
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $status, PDO::PARAM_STR);
            $stmt->bindValue(2, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetTransaction($futuretrxid){
        try{
            
            $query = "SELECT * FROM `transaction`  WHERE n_futuretrxid = ?";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $futuretrxid, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetLastSms($user){
        try{

            $query = "SELECT d_timestamp FROM tbl_sms WHERE v_user = ? ORDER BY d_timestamp DESC LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetSmsLog($date, $user){
        try{

            $query = "SELECT * FROM tbl_sms WHERE v_user = ? AND DATE(d_timestamp) = ? ORDER BY d_timestamp DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $user, PDO::PARAM_STR);
            $stmt->bindValue(2, $date, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetBankAccount($phone){
        try{

            $query = "SELECT * FROM mybank WHERE v_phonenumber = ? AND v_isactive != 'D'";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $phone, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetAgentByBankAccount($bankCode, $bankAccount){
        try{
            $query = "SELECT A.* FROM ms_login A JOIN mybank B ON A.v_phonenumber = B.v_phonenumber 
            WHERE B.v_bankcode = ? AND v_bankaccountno = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(2, $bankAccount, PDO::PARAM_STR);
            $stmt->execute();
            $agent = '';
            while($rowAgent = $stmt->fetch(PDO::FETCH_ASSOC)){
                $agent = $rowAgent['v_user'];
            }
            
            return $agent;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetCurrentCommission($bankCode, $bankAccount){
        try{

            $query = "SELECT * FROM tbl_agent_commission_mutation WHERE v_bankcode = ? AND v_bankaccountno = ? ORDER BY d_insert DESC LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(2, $bankAccount, PDO::PARAM_STR);
            $stmt->execute();
            $current = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $current = floatval($row['n_after']);
            }
            
            return $current;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveMutation($id, $agent, $type, $futureTrxId, $bankCode, $bankAccount, $before, $commissionFee, $after){
        try{

            $query = "INSERT INTO tbl_agent_commission_mutation (v_id, d_insert, v_user, v_type, n_futuretrxid, v_bankcode, v_bankaccountno, n_before, n_commission, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $agent, PDO::PARAM_STR);
            $stmt->bindValue(4, $type, PDO::PARAM_STR);
            $stmt->bindValue(5, $futureTrxId, PDO::PARAM_STR);
            $stmt->bindValue(6, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(7, $bankAccount, PDO::PARAM_STR);
            $stmt->bindValue(8, $before, PDO::PARAM_STR);
            $stmt->bindValue(9, $commissionFee, PDO::PARAM_STR);
            $stmt->bindValue(10, $after, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveMutationBySettlement($id, $agent, $type, $settlementId, $bankCode, $bankAccount, $before, $commissionFee, $after){
        try{

            $query = "INSERT INTO tbl_agent_commission_mutation (v_id, d_insert, v_user, v_type, n_settlementid, v_bankcode, v_bankaccountno, n_before, n_commission, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $agent, PDO::PARAM_STR);
            $stmt->bindValue(4, $type, PDO::PARAM_STR);
            $stmt->bindValue(5, $settlementId, PDO::PARAM_STR);
            $stmt->bindValue(6, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(7, $bankAccount, PDO::PARAM_STR);
            $stmt->bindValue(8, $before, PDO::PARAM_STR);
            $stmt->bindValue(9, $commissionFee, PDO::PARAM_STR);
            $stmt->bindValue(10, $after, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetCommissionSettlementList($from, $to, $user = ''){
        try{
            $query = "SELECT * FROM tbl_agent_commission_settlement WHERE d_insert >= '$from' AND d_insert <= '$to' ";
            if($user != ''){
                $query .= " AND v_user = '$user' ";
            }
            $query .= " ORDER BY d_insert DESC";
            $stmt = $this->connection->prepare($query);
            // $stmt->bindValue(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }
    public function GetCommissionSettlementById($id){
        try{
            $query = "SELECT * FROM tbl_agent_commission_settlement WHERE n_id = ? ";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveCommissionSettlement($user, $bankCode, $accountSource, $accountTarget, $amount){
        try{

            $query = "INSERT INTO tbl_agent_commission_settlement (d_insert, v_user, n_amount, v_bankcode, v_bankaccountsource, v_bankaccounttarget, n_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $user, PDO::PARAM_STR);
            $stmt->bindValue(3, $amount, PDO::PARAM_STR);
            $stmt->bindValue(4, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(5, $accountSource, PDO::PARAM_STR);
            $stmt->bindValue(6, $accountTarget, PDO::PARAM_STR);
            $stmt->bindValue(7, "0", PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function ApproveCommissionSettlement($id, $by){
        try{

            $query = "UPDATE tbl_agent_commission_settlement SET n_status = 1, v_processby =?, d_processdate = ? WHERE n_id =?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $by, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function RejectCommissionSettlement($id, $by, $note){
        try{

            $query = "UPDATE tbl_agent_commission_settlement SET n_status = 2, v_processby =?, d_processdate = ?, v_note = ? WHERE n_id =?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $by, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $note, PDO::PARAM_STR);
            $stmt->bindValue(4, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveAddCredit($user, $amount){
        try{

            $query = "INSERT INTO tbl_agent_credit_request (d_insert, v_user, n_amount, n_status) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $user, PDO::PARAM_STR);
            $stmt->bindValue(3, $amount, PDO::PARAM_STR);
            $stmt->bindValue(4, "0", PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetCreditRequestList($from, $to, $user = ''){
        try{
            $query = "SELECT * FROM tbl_agent_credit_request WHERE d_insert >= '$from' AND d_insert <= '$to' ";
            if($user != ''){
                $query .= " AND v_user = '$user' ";
            }
            $query .= " ORDER BY d_insert DESC";
            $stmt = $this->connection->prepare($query);
            // $stmt->bindValue(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }
    public function GetAddCreditById($id){
        try{

            $query = "SELECT * FROM tbl_agent_credit_request WHERE n_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function ApproveAddCredit($id, $by){
        try{

            $query = "UPDATE tbl_agent_credit_request SET n_status = 1, v_processby =?, d_processdate = ? WHERE n_id =?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $by, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function RejectAddCredit($id, $by, $note){
        try{

            $query = "UPDATE tbl_agent_credit_request SET n_status = 2, v_processby =?, d_processdate = ?, v_note = ? WHERE n_id =?";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $by, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $note, PDO::PARAM_STR);
            $stmt->bindValue(4, $id, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveCommissionMutation($id, $agent, $type, $futuretrxid, $before, $commissionFee, $after){
        try{

            $query = "INSERT INTO tbl_agent_commission_mutation (v_id, d_insert, v_user, v_type, n_futuretrxid, n_before, n_commission, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $agent, PDO::PARAM_STR);
            $stmt->bindValue(4, $type, PDO::PARAM_STR);
            $stmt->bindValue(5, $futuretrxid, PDO::PARAM_STR);
            $stmt->bindValue(6, $before, PDO::PARAM_STR);
            $stmt->bindValue(7, $commissionFee, PDO::PARAM_STR);
            $stmt->bindValue(8, $after, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveCreditMutationByRequest($id, $agent, $type, $transid, $before, $commissionFee, $after, $isTransaction = false){
        try{

            $query = "INSERT INTO tbl_agent_credit_mutation (v_id, d_insert, v_user, v_type, n_requestid, n_before, n_credit, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            if($isTransaction){
                $query = "INSERT INTO tbl_agent_credit_mutation (v_id, d_insert, v_user, v_type, n_futuretrxid, n_before, n_credit, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            }

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $agent, PDO::PARAM_STR);
            $stmt->bindValue(4, $type, PDO::PARAM_STR);
            $stmt->bindValue(5, $transid, PDO::PARAM_STR);
            $stmt->bindValue(6, $before, PDO::PARAM_STR);
            $stmt->bindValue(7, $commissionFee, PDO::PARAM_STR);
            $stmt->bindValue(8, $after, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    

    public function GetCurrentCredit($user){
        try{

            $query = "SELECT * FROM tbl_agent_credit_mutation WHERE v_user = ? ORDER BY d_insert DESC LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            $current = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $current = floatval($row['n_after']);
            }
            
            return $current;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetList(){
        try{

            $query = "SELECT * FROM ms_login WHERE v_logintype = 'G' ORDER BY v_user";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveCreditAdjustment($username, $type, $amount, $by){
        try{

            $query = "INSERT INTO tbl_agent_credit_adjustment (d_insert, v_user, v_type, n_amount, v_adjustByUser) VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->bindValue(3, $type, PDO::PARAM_STR);
            $stmt->bindValue(4, $amount, PDO::PARAM_STR);
            $stmt->bindValue(5, $by, PDO::PARAM_STR);
            $stmt->execute();

            $lastId = $this->connection->lastInsertId();
            
            return $lastId;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveCreditMutationByAdjustment($id, $agent, $type, $transid, $before, $commissionFee, $after){
        try{

            $query = "INSERT INTO tbl_agent_credit_mutation (v_id, d_insert, v_user, v_type, n_creditAdjustmentId, n_before, n_credit, n_after) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $id, PDO::PARAM_STR);
            $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(3, $agent, PDO::PARAM_STR);
            $stmt->bindValue(4, $type, PDO::PARAM_STR);
            $stmt->bindValue(5, $transid, PDO::PARAM_STR);
            $stmt->bindValue(6, $before, PDO::PARAM_STR);
            $stmt->bindValue(7, $commissionFee, PDO::PARAM_STR);
            $stmt->bindValue(8, $after, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt;
            
        }catch(Exception $e){
            throw $e;
        }
    }
    
}

?>