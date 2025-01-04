<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Payments\PaymentService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private PaymentService $paymentService,
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {

         if(($paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value) || 
            ($paymentDTO->paymentStatus == PaymentStatusEnum::NoToken->value)
         ){
            
            if($paymentDTO->callbackResponse == "YES"){
               $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
               Log::info("(".$paymentDTO->urlPrefix.") callback based transaction before posting receipt.".
                     " Mobile: ".$paymentDTO->mobileNumber.
                     " PaymentType: ".$theMenu->paymentType.
                     " PaymentStatus: ".$paymentDTO->paymentStatus.
                     " PPTransactionId: ".$paymentDTO->transactionId.
                     " TransactionId: ".$paymentDTO->paymentStatus.
                     " Token: ".$paymentDTO->tokenNumber.
                     " ReceiptNumber: ".$paymentDTO->receiptNumber);
            }

            if($paymentDTO->receiptNumber  != ''){
               $paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
            }else{
               $paymentDTO = $this->receiptPayment->handle($paymentDTO);
            }

            if($paymentDTO->callbackResponse == "YES"){
               $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
               Log::info("(".$paymentDTO->urlPrefix.") callback based transaction after posting receipt.".
                     " Mobile: ".$paymentDTO->mobileNumber.
                     " PaymentType: ".$theMenu->paymentType.
                     " PaymentStatus: ".$paymentDTO->paymentStatus.
                     " PPTransactionId: ".$paymentDTO->transactionId.
                     " TransactionId: ".$paymentDTO->paymentStatus.
                     " TokenNumber: ".$paymentDTO->tokenNumber.
                     " ReceiptNumber: ".$paymentDTO->receiptNumber);
            }
            
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}