<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class BuyUnits_Step_1
{

   private $checkPaymentsEnabled;
   private $accountNoMenu;
   public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      StepService_AccountNoMenu $accountNoMenu)
   {
      $this->checkPaymentsEnabled=$checkPaymentsEnabled;
      $this->accountNoMenu=$accountNoMenu;
   }

   public function run(BaseDTO $txDTO)
   {

      try {    
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($momoPaymentStatus['enabled']){
            $txDTO->response = $this->accountNoMenu->handle($txDTO->urlPrefix);
         }else{
            $txDTO->response = $momoPaymentStatus['responseText'];
            $txDTO->lastResponse= true;
         }
      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'MoMoOffline';
         }else{
            $txDTO->error = 'Buy units sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
   
}