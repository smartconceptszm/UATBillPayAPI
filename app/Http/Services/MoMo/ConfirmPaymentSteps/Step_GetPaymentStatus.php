<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\External\MoMoClients\IMoMoClient;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_GetPaymentStatus extends EfectivoPipelineContract
{

   public function __construct(
      private IMoMoClient $momoClient)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {
      
      try {
         if($momoDTO->error == ''){
            $mnoResponse = $this->momoClient->confirmPayment($momoDTO->toMoMoParams());
            $momoDTO->mnoTransactionId = $mnoResponse->mnoTransactionId;
            $momoDTO->paymentStatus = $mnoResponse->status;
            $momoDTO->error = $mnoResponse->error;
            if (\strpos($momoDTO->error,'PENDING')){
               $momoDTO->error = $momoDTO->mnoName." response: PENDING";
            }
         }
      } catch (Exception $e) {
         $momoDTO->error='At get payment status pipeline step. '.$e->getMessage();
      }
      return  $momoDTO;
      
   }

}