<?php

include_once __DIR__."/../class/transaction.php";
include_once __DIR__."/../class/merchantBalance.php";
include_once __DIR__."/baseCtrl.php";

class MerchantCtrl extends BaseCtrl {

    public function __construct($conn = null){
        parent::__construct($conn);
    }

    /**
     * Get current balance
     * 
     * @author rusman
     * @since v3.3.0
     * @param string $merchantCode 
     */
    public function GetCurrentBalance($merchantCode){
        try{

            $merchantBalanceClass = new MerchantBalance($this->connection);
            $transaction = new Transaction($this->connection);

            $lastBalanceRow = $merchantBalanceClass->GetLastBalanceSummary($merchantCode);

            $openingBalance = 0;
            $year = date('Y');
            $month = date('n')-1;
            if($month == 0) {
                $year -= 1;
                $month = 12;
            }

            if($lastBalanceRow != null){
                $openingBalance = $lastBalanceRow['n_closing_balance'];
                $year = $lastBalanceRow['n_year'];
                $month = intval($lastBalanceRow['n_month']);
            }

            $month+=1;
            if($month > 12) {
                $month = 1;
                $year += 1;
            }

            $startDate = $year."-".($month > 9 ? $month : '0'.$month)."-01";

            $lastDaily = $merchantBalanceClass->GetLastDaily($merchantCode);
            $endDate = $lastDaily['d_date'];

            $listDaily = $merchantBalanceClass->GetBalanceDaily($merchantCode, $startDate." 00:00:00", $endDate. " 23:59:59");
            
            while($rowDaily = $listDaily->fetch(PDO::FETCH_ASSOC)){
                $closingBalance = $openingBalance + floatval($rowDaily['n_deposit']) - floatval($rowDaily['n_withdraw']) + floatval($rowDaily['n_topup']) - floatval($rowDaily['n_settlement']) - floatval($rowDaily['n_fee'])
                    + floatval($rowDaily['n_adjustmentin']) - floatval($rowDaily['n_adjustmentout']) 
                    + floatval($rowDaily['n_trans_adjustmentin']) - floatval($rowDaily['n_trans_adjustmentout']) - floatval($rowDaily['n_trans_adjustment_fee']);
                $openingBalance = $closingBalance;    
            }
            
            //check for next day
            $nextDate = date('Y-m-d', strtotime($endDate . " + 1 days"));
            $from = $nextDate." 00:00:00";
            $to = $nextDate." 23:59:59";

            $totalDeposit = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'D');
            $totalWithdraw = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'W');
            $totalTopup = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'N');
            $totalSettlement = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'M');
            $totalAdjustmentIn = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'Y');
            $totalAdjustmentOut = $transaction->SummaryByDateNew($merchantCode, $from, $to, 'Z');

            $totalFee = $transaction->SummaryFeeByDateNew($merchantCode, $from, $to, false);
            
            $closingBalance = $openingBalance + $totalDeposit - $totalWithdraw + $totalTopup - $totalSettlement + $totalAdjustmentIn - $totalAdjustmentOut - $totalFee;

            return $closingBalance;

        }catch(Exception $e){
            throw $e;
        }
    }
}

?>