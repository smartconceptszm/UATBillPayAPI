<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_SendPaymentsProviderRequest extends EfectivoPipelineContract
{

   public function __construct(
      private IPaymentsProviderClient $paymentsProviderClient)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if($paymentDTO->error==''){
            $paymentsProviderResponse = $this->paymentsProviderClient->requestPayment($paymentDTO->toProviderParams());
            $paymentDTO->transactionId = $paymentsProviderResponse->transactionId;
            $paymentDTO->paymentStatus = $paymentsProviderResponse->status;
            $paymentDTO->error = $paymentsProviderResponse->error;
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At send momo request. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}