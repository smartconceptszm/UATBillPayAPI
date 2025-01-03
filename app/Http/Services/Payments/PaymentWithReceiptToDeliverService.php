<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use App\Http\Services\Enums\PaymentTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentWithReceiptToDeliverService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private ClientMenuService $clientMenuService,
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function update(string $id):string{
      try {
         
         $thePayment = $this->paymentToReviewService->findById($id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));

         if ($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value || 
                                 $paymentDTO->paymentStatus == PaymentStatusEnum::Receipt_Delivered->value ) {  

            if(!$paymentDTO->receipt){
               
               $paymentDTO->receipt = "Payment successful\n";
               $paymentDTO->receipt.= "Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n";
               $paymentDTO->receipt.= "Acc: " . $paymentDTO->customerAccount . "\n";

               $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
               if($theMenu->paymentType == PaymentTypeEnum::PrePaid->value){
                  $paymentDTO->receipt.= "Token: ". $paymentDTO->tokenNumber . "\n";
               }else{
                  $paymentDTO->receipt.= "Rcpt No: " . $paymentDTO->receiptNumber . "\n";
               }
               
               $paymentDTO->receipt.="Date: " . Carbon::parse($paymentDTO->updated_at)->format('d-M-Y'). "\n";
            }

            if($user = Auth::user()){
               $paymentDTO->user_id = $user->id;
            }

            $paymentDTO = $this->confirmPayment->handle($paymentDTO);

         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO->receipt;
   }
}
