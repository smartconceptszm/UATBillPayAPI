<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Auth;
use App\Http\DTOs\MoMoDTO;
use Exception;

class ReceiptService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function create(array $data):object|null{
      
      try {

         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if($paymentDTO->paymentStatus != PaymentStatusEnum::Paid->value){
            throw new Exception("Unexpected payment status - ".$paymentDTO->paymentStatus);
         }

         if($paymentDTO->receiptNumber != ''){
            throw new Exception("Payment appears receipted! Receipt number ".$paymentDTO->receiptNumber);
         }

         if($paymentDTO->ppTransactionId == ''){
            throw new Exception("MNO transaction Id is null. Payment not yet confirmed!");
         }  

         if($user = Auth::user()){
            $paymentDTO->user_id = $user->id;
         }

         $paymentDTO->error = "";
         $paymentDTO = $this->confirmPayment->handle($paymentDTO);
      
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO;
      
   }

}
