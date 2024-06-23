<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_GetPaymentStatus extends EfectivoPipelineContract
{

   public function __construct(
      private IPaymentsProviderClient $paymentsProviderClient)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         if($paymentDTO->error == ''){
            $paymentsProviderResponse = $this->paymentsProviderClient->confirmPayment($paymentDTO->toProviderParams());
            $paymentDTO->ppTransactionId = $paymentsProviderResponse->ppTransactionId;
            $paymentDTO->paymentStatus = $paymentsProviderResponse->status;
            $paymentDTO->error = $paymentsProviderResponse->error;
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At get payment status pipeline step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}