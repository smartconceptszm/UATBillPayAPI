<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use App\Http\Services\Enums\PaymentTypeEnum;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\MoMoDTO;


use Exception;

class MoMoCallbackService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function handleAirtel(array $callbackParams):string{
      
      try {
         
      
         $thePayment = $this->paymentToReviewService->findByTransactionId($callbackParams['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         $paymentDTO->ppTransactionId = $callbackParams['airtel_money_id'];
         $paymentDTO->callbackResponse = "YES";
         //Log::info("(".$paymentDTO->urlPrefix.") Airtel money callback executed on wallet: ".$paymentDTO->mobileNumber);
         
         if($callbackParams['status_code'] == 'TS'){
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            if($theMenu->paymentType == PaymentTypeEnum::PrePaid->value){
               $paymentDTO->paymentStatus = PaymentStatusEnum::NoToken->value;
            }else{
               $paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
            }
            $paymentDTO->error = "";
         }else{
            $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
            $paymentDTO->error = $callbackParams['message'];
         }

         $paymentDTO = $this->confirmPayment->handle($paymentDTO);
   
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return "Handled";
      
   }

}
