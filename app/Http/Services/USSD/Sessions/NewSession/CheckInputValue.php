<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckInputValue extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if($txDTO->isNewRequest == '1' && $txDTO->subscriberInput == ''){
            $txDTO->subscriberInput = "115";//$txDTO->shortCode ;
         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}