<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if(\count(\explode("*", $txDTO->customerJourney))==1){
         $txDTO->stepProcessed = true;
         try {
            $txDTO->response = $this->accountNoMenu->handle('',$txDTO->urlPrefix);
         } catch (Exception $e) {
            $txDTO->error = 'Update details step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}