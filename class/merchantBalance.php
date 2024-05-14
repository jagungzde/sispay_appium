<?php

class MerchantBalance{
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function Save($merchantCode, $year, $month, $opening, $deposit, $withdraw, $topup, $settlement, $adjustmentIn, $adjustmentOut, $fee, $agentFee, $closing, $trans_adjustin, $trans_adjustout, $trans_adjustfee, $trans_adjustcommission){
        try{
            $query = "INSERT INTO tbl_merchant_balance_summary 
            (v_merchantcode, n_year, n_month, n_opening_balance, n_deposit, n_withdraw, n_topup, n_settlement, n_adjustmentin, n_adjustmentout, n_fee, n_agent_commission, n_closing_balance
            , n_trans_adjustmentin, n_trans_adjustmentout, n_trans_adjustment_fee, n_trans_adjustment_agent_commission) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $opening, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $deposit, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $withdraw, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $topup, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $settlement, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentFee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closing, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustin, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustout, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustfee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustcommission, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function SummaryExists($merchantCode, $year, $month){
        try{
            $query = "SELECT * FROM tbl_merchant_balance_summary WHERE v_merchantcode = ? AND n_year =? AND n_month =? ";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->execute();

            if($stmt->rowCount() > 0)
                return $stmt->fetch(PDO::FETCH_ASSOC);
            else
                return false;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function Update($merchantCode, $year, $month, $opening, $deposit, $withdraw, $topup, $settlement, $adjustmentIn, $adjustmentOut, $fee, $agentFee, $closing, $trans_adjustin, $trans_adjustout, $trans_adjustfee, $trans_adjustcommission){
        try{
            $query = "UPDATE tbl_merchant_balance_summary SET n_opening_balance =?, n_deposit=?, n_withdraw =?, n_topup=?, 
                n_settlement=?, n_adjustmentin=?, n_adjustmentout=?, n_fee=?, n_agent_commission=?, n_closing_balance=?,
                n_trans_adjustmentin = ?, n_trans_adjustmentout = ?, n_trans_adjustment_fee =?, n_trans_adjustment_agent_commission = ?
                WHERE v_merchantcode = ? AND n_year = ? AND n_month = ?";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $opening, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $deposit, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $withdraw, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $topup, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $settlement, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentFee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closing, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustin, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustout, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustfee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $trans_adjustcommission, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function SaveDaily($merchantCode, $date, $opening, $deposit, $withdraw, $topup, $settlement, $adjustmentIn, $adjustmentOut, $fee, $agentFee, $closing, $finalOpening = null, $finalClosing = null){
        try{

            if($finalOpening == null) $finalOpening = $opening;
            if($finalClosing == null) $finalClosing = $closing;

            $query = "INSERT INTO tbl_merchant_balance_daily (v_merchantcode, d_date, n_opening_balance, n_deposit, n_withdraw, n_topup, n_settlement, n_adjustmentin, n_adjustmentout, n_fee, n_agent_commission, n_closing_balance, n_final_opening, n_final_closing) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $opening, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $deposit, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $withdraw, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $topup, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $settlement, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentFee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closing, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $finalOpening, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $finalClosing, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function UpdateDaily($merchantCode, $date, $opening, $deposit, $withdraw, $topup, $settlement, $adjustmentIn, $adjustmentOut, $fee, $agentFee, $closing){
        try{
            $query = "UPDATE tbl_merchant_balance_daily SET n_opening_balance=?, n_deposit=?, n_withdraw=?, n_topup=?, n_settlement=?, n_adjustmentin=?, n_adjustmentout=?, n_fee=?, n_agent_commission=?, n_closing_balance=?
            WHERE v_merchantcode = ? AND d_date = ?";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $opening, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $deposit, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $withdraw, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $topup, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $settlement, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $adjustmentOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentFee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closing, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetLastBalanceSummary($merchantCode, $year = 0, $month = 0){
        try{

            if($merchantCode != "ALL"){
                $query = "SELECT * FROM tbl_merchant_balance_summary WHERE v_merchantcode = ? ORDER BY n_year DESC, n_month DESC LIMIT 1";
                if($year != 0 && $month != 0){
                    $query = "SELECT * FROM tbl_merchant_balance_summary WHERE v_merchantcode = ? AND (n_year < $year OR (n_year = $year AND n_month <= $month))  ORDER BY n_year DESC, n_month DESC LIMIT 1";
                }
            }else{
                $query = "SELECT n_year, n_month, IFNULL(SUM(n_closing_balance),0) as n_closing_balance FROM tbl_merchant_balance_summary GROUP BY n_year, n_month ORDER BY n_year DESC, n_month DESC LIMIT 1";
                if($year != 0 && $month != 0){
                    $query = "SELECT n_year, n_month, IFNULL(SUM(n_closing_balance),0) as n_closing_balance FROM tbl_merchant_balance_summary WHERE (n_year < $year OR (n_year = $year AND n_month <= $month)) GROUP BY n_year, n_month ORDER BY n_year DESC, n_month DESC LIMIT 1";
                }
            }
            

            $stmt = $this->connection->prepare($query);
            $i = 1;
            if($merchantCode != "ALL")
                $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->execute();

            if($stmt->rowCount() > 0)
                return $stmt->fetch(PDO::FETCH_ASSOC);
            else
                return false;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetLastDaily($merchantCode){
        try{
            $query = "SELECT * FROM tbl_merchant_balance_daily WHERE v_merchantcode = ? ORDER BY d_date DESC LIMIT 1";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->execute();

            if($stmt->rowCount() > 0)
                return $stmt->fetch(PDO::FETCH_ASSOC);
            else
                return false;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetBalanceDaily($merchantCode, $start, $from){
        try{
            if($merchantCode != 'ALL'){
                $query = "SELECT * FROM tbl_merchant_balance_daily WHERE v_merchantcode = ? AND d_date >= '$start' AND d_date <= '$from'  ORDER BY d_date ASC";
            }else{
                $query = "SELECT d_date, IFNULL(SUM(n_deposit), 0) as n_deposit, 
                    IFNULL(SUM(n_withdraw), 0) as n_withdraw,
                    IFNULL(SUM(n_topup), 0) as n_topup,
                    IFNULL(SUM(n_settlement), 0) as n_settlement,
                    IFNULL(SUM(n_fee), 0) as n_fee,
                    IFNULL(SUM(n_adjustmentin), 0) as n_adjustmentin,
                    IFNULL(SUM(n_adjustmentout), 0) as n_adjustmentout,
                    IFNULL(SUM(n_trans_adjustmentin), 0) as n_trans_adjustmentin,
                    IFNULL(SUM(n_trans_adjustmentout), 0) as n_trans_adjustmentout,
                    IFNULL(SUM(n_trans_adjustment_fee), 0) as n_trans_adjustment_fee
                FROM tbl_merchant_balance_daily WHERE d_date >= '$start' AND d_date <= '$from' GROUP BY d_date ORDER BY d_date ASC";
            }
            
            $stmt = $this->connection->prepare($query);
            $i = 1;
            if($merchantCode != "ALL")
                $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetBalanceAndReportDaily($merchantCode, $start, $from){
      try{
          if($merchantCode != 'ALL'){
              $query = "SELECT a.*, b.n_deposit rDeposit, b.n_withdraw rWithdraw, b.n_fee rFee, b.n_deposit_adjustment rDepositAdjustment, b.n_withdraw_adjustment rWithdrawAdjustment, b.n_fee_adjustment rFeeAdjustment FROM tbl_merchant_balance_daily a join tbl_report_sum_daily b on a.v_merchantcode = b.v_merchantcode AND a.d_date = b.d_date WHERE a.v_merchantcode = ? AND a.d_date >= '$start' AND a.d_date <= '$from'  ORDER BY a.d_date ASC";
          }else{
              $query = "SELECT a.d_date, IFNULL(SUM(a.n_deposit), 0) as n_deposit, 
                  IFNULL(SUM(a.n_withdraw), 0) as n_withdraw,
                  IFNULL(SUM(a.n_topup), 0) as n_topup,
                  IFNULL(SUM(a.n_settlement), 0) as n_settlement,
                  IFNULL(SUM(a.n_fee), 0) as n_fee,
                  IFNULL(SUM(a.n_adjustmentin), 0) as n_adjustmentin,
                  IFNULL(SUM(a.n_adjustmentout), 0) as n_adjustmentout,
                  IFNULL(SUM(a.n_trans_adjustmentin), 0) as n_trans_adjustmentin,
                  IFNULL(SUM(a.n_trans_adjustmentout), 0) as n_trans_adjustmentout,
                  IFNULL(SUM(a.n_trans_adjustment_fee), 0) as n_trans_adjustment_fee,
                  b.v_merchantcode,
                  b.n_deposit rDeposit, 
                  b.n_withdraw rWithdraw, 
                  b.n_fee rFee, 
                  b.n_deposit_adjustment rDepositAdjustment, 
                  b.n_withdraw_adjustment rWithdrawAdjustment, 
                  b.n_fee_adjustment rFeeAdjustment
              FROM tbl_merchant_balance_daily a join tbl_report_sum_daily b on b.v_merchantcode = 'ALL' and a.d_date = b.d_date WHERE a.d_date >= '$start' AND a.d_date <= '$from' GROUP BY a.d_date ORDER BY a.d_date ASC";
          }
          
          $stmt = $this->connection->prepare($query);
          $i = 1;
          if($merchantCode != "ALL")
              $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
          $stmt->execute();

          return $stmt;

      }catch(Exception $e){
          throw $e;
      }
  }

    public function GetBalanceDailyByDate($merchantCode, $date){
        try{
            $query = "SELECT * FROM tbl_merchant_balance_daily WHERE v_merchantcode = ? AND d_date = '$date'  ";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function SummaryFromDaily($merchantCode, $year, $month){
        try{
            $query = "SELECT 
                    IFNULL(SUM(n_deposit),0) as deposit, 
                    IFNULL(SUM(n_withdraw),0) as withdraw, 
                    IFNULL(SUM(n_fee),0) as fee, 
                    IFNULL(SUM(n_agent_commission),0) as agent_commission, 
                    IFNULL(SUM(n_topup),0) as topup, 
                    IFNULL(SUM(n_settlement),0) as settlement, 
                    IFNULL(SUM(n_adjustmentin),0) as adjustmentin, 
                    IFNULL(SUM(n_adjustmentout),0) as adjustmentout,
                    IFNULL(SUM(n_trans_adjustmentin),0) as trans_adjustmentin,
                    IFNULL(SUM(n_trans_adjustmentout),0) as trans_adjustmentout,
                    IFNULL(SUM(n_trans_adjustment_fee),0) as trans_adjustment_fee,
                    IFNULL(SUM(n_trans_adjustment_agent_commission),0) as trans_adjustment_agent_commission
                FROM tbl_merchant_balance_daily 
                WHERE v_merchantcode = ? AND YEAR(d_date) = ? AND MONTH(d_date) = ?  ";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Add transaction adjustment into merchant balance daily
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string $merchantcode 
     * @param string $date
     * @param float $amount
     * @param float $fee
     * @param float $agentCommission 
     * @param bool $isIn
     * 
     */
    public function AddTransactionAdjustment($merchantCode, $date, $amountIn, $amountOut, $fee, $agentCommission){
        try{

            $query = "UPDATE `tbl_merchant_balance_daily` SET n_trans_adjustmentin = n_trans_adjustmentin + ?, n_trans_adjustmentout = n_trans_adjustmentout + ?, n_trans_adjustment_fee = n_trans_adjustment_fee + ?, n_trans_adjustment_agent_commission = n_trans_adjustment_agent_commission + ?  WHERE v_merchantcode = ? AND d_date = ?";
            
            $stmt = $this->connection->prepare($query);
            $i=1;
            $stmt->bindValue($i, $amountIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $amountOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentCommission, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Update transaction adjustment into merchant balance daily (not add)
     * 
     * @author rusman
     * @since v3.3.0
     * 
     * @param string $merchantcode 
     * @param string $date
     * @param float $amount
     * @param float $fee
     * @param float $agentCommission 
     * @param bool $isIn
     * 
     */
    public function UpdateTransactionAdjustment($merchantCode, $date, $amountIn, $amountOut, $fee, $agentCommission){
        try{

            $query = "UPDATE `tbl_merchant_balance_daily` SET n_trans_adjustmentin = ?, n_trans_adjustmentout =  ?, n_trans_adjustment_fee =  ?, n_trans_adjustment_agent_commission = ? 
                 WHERE v_merchantcode = ? AND d_date = ?";
            
            $stmt = $this->connection->prepare($query);
            $i=1;
            $stmt->bindValue($i, $amountIn, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $amountOut, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentCommission, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Add transaction adjustment into merchant balance summary
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string $merchantcode 
     * @param int $year
     * @param int $month
     * @param float $amount
     * @param float $fee
     * @param float $agentCommission 
     * @param bool $isIn
     * 
     */
    public function AddTransactionAdjustmentSummary($merchantCode, $year, $month, $amount, $fee, $agentCommission, $isIn){
        try{

            $query = "UPDATE `tbl_merchant_balance_summary` SET n_trans_adjustmentin = n_trans_adjustmentin + ?, n_trans_adjustment_fee = n_trans_adjustment_fee + ?, n_trans_adjustment_agent_commission = n_trans_adjustment_agent_commission + ?  WHERE v_merchantcode = ? AND n_year = ? AND n_month =?";
            if(!$isIn){
                $query = "UPDATE `tbl_merchant_balance_summary` SET n_trans_adjustmentout = n_trans_adjustmentin + ?, n_trans_adjustment_fee = n_trans_adjustment_fee + ?, n_trans_adjustment_agent_commission = n_trans_adjustment_agent_commission + ?  WHERE v_merchantcode = ? AND n_year = ? AND n_month =?";
            }
            $stmt = $this->connection->prepare($query);
            $i=1;
            $stmt->bindValue($i, $amount, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $fee, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $agentCommission, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * update opening final dan closing final daily
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string $merchantcode 
     * @param int $date
     * @param float $openingFinal
     * @param float $closingFinal
     * 
     */
    public function UpdateFinalDaily($merchantCode, $date, $openingFinal, $closingFinal){
        try{
            $query = "UPDATE tbl_merchant_balance_daily SET n_final_opening=?, n_final_closing=? WHERE v_merchantcode = ? AND d_date = ?";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $openingFinal, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closingFinal, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $date, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * update opening final dan closing final summary
     * 
     * @author rusman
     * @since v3.2.0
     * 
     * @param string $merchantcode 
     * @param int $year
     * @param int $month
     * @param float $openingFinal
     * @param float $closingFinal
     * 
     */
    public function UpdateFinalSummary($merchantCode, $year, $month, $openingFinal, $closingFinal){
        try{
            $query = "UPDATE tbl_merchant_balance_summary SET n_final_opening=?, n_final_closing=? WHERE v_merchantcode = ? AND n_year = ? AND n_month = ?";

            $stmt = $this->connection->prepare($query);
            $i = 1;
            $stmt->bindValue($i, $openingFinal, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $closingFinal, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $merchantCode, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $year, PDO::PARAM_STR); $i++;
            $stmt->bindValue($i, $month, PDO::PARAM_STR); $i++;
            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw $e;
        }
    }
}

?>