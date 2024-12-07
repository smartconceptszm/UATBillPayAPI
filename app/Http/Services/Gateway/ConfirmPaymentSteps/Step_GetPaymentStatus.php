<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Enums\PaymentTypeEnum;
use App\Http\DTOs\BaseDTO;

class Step_GetPaymentStatus extends EfectivoPipelineContract
{

   public function __construct(
      private IPaymentsProviderClient $paymentsProviderClient,
      private ClientMenuService $clientMenuService)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         
         if( 
               ($paymentDTO->paymentStatus == PaymentStatusEnum::Submitted->value)||
               ($paymentDTO->paymentStatus == PaymentStatusEnum::Payment_Failed->value) ||
               ($paymentDTO->paymentStatus == PaymentStatusEnum::Submission_Failed->value)
         ){
            $paymentsProviderResponse = $this->paymentsProviderClient->confirmPayment($paymentDTO->toProviderParams());
            if($paymentsProviderResponse->status == "PAYMENT SUCCESSFUL"){
               $paymentDTO->ppTransactionId = $paymentsProviderResponse->ppTransactionId;
               $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
               if($theMenu->paymentType == PaymentTypeEnum::PrePaid->value){
                  $paymentDTO->paymentStatus = PaymentStatusEnum::NoToken->value;
               }else{
                  $paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
               }
            }else{
               $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
               $paymentDTO->error = $paymentsProviderResponse->error;
            }
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At get payment status pipeline step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}