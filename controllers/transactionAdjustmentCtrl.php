<?php

include_once __DIR__."/../class/transaction.php";
include_once __DIR__."/../class/merchantBalance.php";
include_once __DIR__."/baseCtrl.php";

class TransactionAdjustmentCtrl extends BaseCtrl {

    public function __construct($conn = null){
        parent::__construct($conn);
    }

    /**
     * Input the transaction into transaction adjustment, update merchant balance daily & summary
     * then update transaction as ajdust and adjustdate
     * 
     * @author rusman
     * @since v3.2.0
     * @param int $futureTrxId futuretrxid from transaction
     */
    public function doTransactionAdjustment($futureTrxId){
        try{
            if($futureTrxId <= 0) throw new Exception('Invalid Future Trx ID');

            $transactionClass = new Transaction($this->connection);
            $merchantBalanceClass = new MerchantBalance($this->connection);

            $transactionData = null;
            $isHistory = false;

            $stmtTrans = $transactionClass->GetTransactionByFutureId($futureTrxId);
            if(count($stmtTrans) == 0){
                $stmtTrans = $transactionClass->GetTransactionHistoryByFutureId($futureTrxId);
                if(count($stmtTrans) == 0){
                    $transactionData = $stmtTrans[0];
                    $isHistory = true;
                }else{
                    throw new Exception('Transaction not found');
                }
            }else{
                $transactionData = $stmtTrans[0];
            }

            // //validate if daily already generated or not
            // $date = explode(" ", $transactionData['d_insert'])[0];
            // $balanceDaily = $merchantBalanceClass->GetBalanceDailyByDate($transactionData['v_merchantcode'], $date);
            // if($balanceDaily->rowCount() == 0){
            //     throw new Exception('Balance daily not available yet');
            // }


            $stmtAdd = $transactionClass->AddTransactionAdjustment($transactionData);
            if($stmtAdd->rowCount() > 0)
            {
                // $stmtUpdate = $transactionClass->SetTransactionAsAdjust($transactionData['n_futuretrxid'], $isHistory);

                // $date = explode(" ", $transactionData['d_insert'])[0];

                // //check merchant balance daily
                // $stmtDaily = $merchantBalanceClass->GetBalanceDailyByDate($transactionData['v_merchantcode'], $date);
                // if($stmtDaily->rowCount() > 0)
                // {
                //     // add to merchant balance daily
                //     $stmtAdd =  $merchantBalanceClass->AddTransactionAdjustment($transactionData['v_merchantcode'], $date, $transactionData['n_amount'], $transactionData['n_fee'], $transactionData['n_agentCommissionFee'], ($transactionData['v_transactiontype'] == 'D' ? true : false) );

                //     // add to merchant balance summary
                //     $arr = explode("-", $date);
                //     $summaryData = $merchantBalanceClass->SummaryExists($transactionData['v_merchantcode'], $arr[0], $arr[1]);
                //     if($summaryData != false){
                //         $stmtAdd =  $merchantBalanceClass->AddTransactionAdjustmentSummary($transactionData['v_merchantcode'], $arr[0], $arr[1], $transactionData['n_amount'], $transactionData['n_fee'], $transactionData['n_agentCommissionFee'], ($transactionData['v_transactiontype'] == 'D' ? true : false));
                //     }else{
                //         //merchant balande summary not found
                //     }
                    
                //     //recalculate opening final and closing final
                //     $recalResult = $this->RecalculateMerchantBalanceDailyFinal($transactionData['v_merchantcode'], $date, $merchantBalanceClass);

                //     $recalSummaryResult = $this->RecalculateMerchantBalanceSummaryFinal($transactionData['v_merchantcode'], $date, $merchantBalanceClass);

                // }else{
                //     //merchant balance daily not found
                // }
            }else{
                throw new Exception("Failed Insert Transaction Adjustment");
            }

        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Recalculate merchant balance daily after adjustment
     * 
     * @author rusman
     * @since v3.2.0
     * @param string $merchantCode 
     * @param string $startDate
     * @param class $merchantBalanceClass depedency class
     */
    public function RecalculateMerchantBalanceDailyFinal($merchantCode, $startDate, $merchantBalanceClass = null){
        try{
            if($merchantBalanceClass == null){
                $merchantBalanceClass = new MerchantBalance($this->connection);
            }

            $lastDailyData = $merchantBalanceClass->GetLastDaily($merchantCode);
            $lastDate = '';

            if($lastDailyData != false){
                $lastDate = $lastDailyData['d_date'];
            }       

            $balanceDailyData = $merchantBalanceClass->GetBalanceDaily($merchantCode, $startDate, $lastDate);
            $opening = null;
            $arr = array();
            while($row = $balanceDailyData->fetch(PDO::FETCH_ASSOC)){
                
                if($opening == null){
                    $opening = $row['n_final_opening'];
                }else{
                    $row['n_final_opening'] = $opening;
                }

                $transAdjustment = floatval($row['n_trans_adjustmentin']) - floatval($row['n_trans_adjustmentout']) - floatval($row['n_trans_adjustment_fee']);

                $closingBalance = $opening + floatval($row['n_deposit']) - floatval($row['n_withdraw']) + floatval($row['n_topup']) - floatval($row['n_settlement']) + floatval($row['n_adjustmentin']) - floatval($row['n_adjustmentout']) - floatval($row['n_fee']) + $transAdjustment;


                $row['n_final_closing'] = $closingBalance;
                array_push($arr, $row);

                $result = $merchantBalanceClass->UpdateFinalDaily($merchantCode, $row['d_date'], $opening, $closingBalance);

                $opening = $closingBalance;
            }
            
            return true;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Recalculate merchant balance summary after adjustment
     * 
     * @author rusman
     * @since v3.2.0
     * @param string $merchantCode 
     * @param string $startDate
     * @param class $merchantBalanceClass depedency class
     */
    public function RecalculateMerchantBalanceSummaryFinal($merchantCode, $startDate, $merchantBalanceClass = null){
        try{
            if($merchantBalanceClass == null){
                $merchantBalanceClass = new MerchantBalance($this->connection);
            }

            $arr = explode("-", $startDate);
            $year = $arr[0];
            $month = $arr[1];

            $lastSummaryData = $merchantBalanceClass->GetLastBalanceSummary($merchantCode);
            $lastYear = '';
            $lastMonth = '';

            if($lastSummaryData != false){
                $lastYear = $lastSummaryData['n_year'];
                $lastMonth = $lastSummaryData['n_month'];
            }else{
                return false;
            }

            $isFinish = false;
            $opening = null;

            while(!$isFinish){

                $summaryData = $merchantBalanceClass->SummaryExists($merchantCode, $year, $month);
                if(!$summaryData){
                    if($opening == null){
                        $opening = $summaryData['n_final_opening'];
                    }else{
                        $summaryData['n_final_opening'] = $opening;
                    }

                    $transAdjustment = floatval($summaryData['n_trans_adjustmentin']) - floatval($summaryData['n_trans_adjustmentout']) - floatval($summaryData['n_trans_adjustment_fee']);

                    $closingBalance = $opening + floatval($summaryData['n_deposit']) - floatval($summaryData['n_withdraw']) + floatval($summaryData['n_topup']) - floatval($summaryData['n_settlement']) + floatval($summaryData['n_adjustmentin']) - floatval($summaryData['n_adjustmentout']) - floatval($summaryData['n_fee']) + $transAdjustment;

                    $row['n_final_closing'] = $closingBalance;

                    $result = $merchantBalanceClass->UpdateFinalSummary($merchantCode, $year, $month, $opening, $closingBalance);

                    $opening = $closingBalance;
                }
                
                $month += 1;
                if($month > 12){
                    $month = 1;
                    $year+=1;
                }

                if($year == $lastYear && $month > $lastMonth) $isFinish = true;
            }
            
            return true;
        }catch(Exception $e){
            throw $e;
        }
    }
}

?>