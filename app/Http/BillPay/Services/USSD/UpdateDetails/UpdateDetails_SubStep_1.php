<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class UpdateDetails_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $accountNoMenu;
   public function __construct(StepService_AccountNoMenu $accountNoMenu)
   {
      $this->accountNoMenu=$accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if(\count(\explode("*", $txDTO->customerJourney))==1){
         $txDTO->stepProcessed=true;
         try {
            $txDTO->response=$this->accountNoMenu->handle('',$txDTO->urlPrefix);
         } catch (\Throwable $e) {
            $txDTO->error = 'Update details step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}