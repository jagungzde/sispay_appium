<?php
include_once __DIR__."/baseCtrl.php";
include_once __DIR__."/../class/agent.php";

class AgentCtrl extends BaseCtrl{

    private $agentClass = null;

    public function __construct($conn = null){
        parent::__construct($conn);
        if($conn != null){
            $this->agentClass = new Agent($conn);
        }   
    }

    public function SubstractCreditByUser($user, $amount, $futureId){
        try{

            $id = parent::GUID();

            $currentCredit = $this->agentClass->GetCurrentCredit($user);

            $mutationCredit = floatval($amount);

            $endCredit = $currentCredit - $mutationCredit;


            $stmt = $this->agentClass->SaveCreditMutationByRequest($id, $user, 'OUT', $futureId, $currentCredit, $mutationCredit, $endCredit, true);

            return true;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function AddCreditByUser($user, $amount, $futureId){
        try{

            $id = parent::GUID();

            $currentCredit = $this->agentClass->GetCurrentCredit($user);

            $mutationCredit = floatval($amount);

            $endCredit = $currentCredit + $mutationCredit;


            $stmt = $this->agentClass->SaveCreditMutationByRequest($id, $user, 'IN', $futureId, $currentCredit, $mutationCredit, $endCredit, true);

            return true;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function AdjustCreditByUser($user, $type, $amount, $creditAdjustmentId){
        try{

            $id = parent::GUID();

            $currentCredit = $this->agentClass->GetCurrentCredit($user);

            $mutationCredit = floatval($amount);

            if(strtoupper($type) == 'IN')
                $endCredit = $currentCredit + $mutationCredit;
            else
                $endCredit = $currentCredit - $mutationCredit;


            $stmt = $this->agentClass->SaveCreditMutationByAdjustment($id, $user, strtoupper($type), $creditAdjustmentId, $currentCredit, $mutationCredit, $endCredit);

            return true;

        }catch(Exception $e){
            throw $e;
        }
    }

}

?>