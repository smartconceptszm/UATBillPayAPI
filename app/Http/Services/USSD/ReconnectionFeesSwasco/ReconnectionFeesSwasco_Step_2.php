<?php

namespace App\Http\Services\USSD\ReconnectionFeesSwasco;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class ReconnectionFeesSwasco_Step_2
{

   public function __construct(
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->response = $this->accountNoMenu->handle($txDTO);
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = "InvalidInput";
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At pay reconnection fees step 2. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}