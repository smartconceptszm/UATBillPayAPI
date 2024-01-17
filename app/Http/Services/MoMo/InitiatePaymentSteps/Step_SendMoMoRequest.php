<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\External\MoMoClients\IMoMoClient;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_SendMoMoRequest extends EfectivoPipelineContract
{

   public function __construct(
      private IMoMoClient $momoClient)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if($momoDTO->error==''){
            $mnoResponse = $this->momoClient->requestPayment($momoDTO->toMoMoParams());
            $momoDTO->transactionId = $mnoResponse->transactionId;
            $momoDTO->paymentStatus = $mnoResponse->status;
            $momoDTO->error = $mnoResponse->error;
            $momoDTO->mnoResponse = $mnoResponse;
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At send momo request. '.$e->getMessage();
      }
      return $momoDTO;

   }

}