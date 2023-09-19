<?php

namespace App\Http\Services\Payments;

use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\MoMo\Utility\Step_LogStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentWithReceiptToDeliverService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private MoMoDTO $momoDTO)
   {}

   public function update(array $data, string $id):object|null{
      try {
         $thePayment = $this->paymentToReviewService->findById($id);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         if ($momoDTO->paymentStatus == 'RECEIPTED' || $momoDTO->paymentStatus == 'RECEIPT DELIVERED' ) {  
            if(!$momoDTO->receipt){
               //consider a format receipt public method for each billing client
               $momoDTO->receipt = "Payment successful\n" .
                  "Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
                  "Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
                  "Acc: " . $momoDTO->accountNumber . "\n";
               $momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
            }
            $user = Auth::user(); 
            $momoDTO->user_id = $user->id;
            $momoDTO =  app(Pipeline::class)
               ->send($momoDTO)
               ->through(
                  [
                     Step_SendReceiptViaSMS::class,
                     Step_UpdateTransaction::class,  
                     Step_LogStatus::class 
                  ]
               )
               ->thenReturn();
         }
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $momoDTO;
   }
}
