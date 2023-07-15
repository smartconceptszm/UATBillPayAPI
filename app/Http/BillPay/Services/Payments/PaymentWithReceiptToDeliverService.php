<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\BillPay\Repositories\Payments\PaymentToReviewRepo;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Exception;

class PaymentWithReceiptToDeliverService implements IUpdateService
{

   private $repository;
   public function __construct(PaymentToReviewRepo $repository)
   {
      $this->repository=$repository;
   }

   public function update(array $data, string $id):object|null{
      try {
         $momoDTO=$this->repository->findById($id);
         if ($momoDTO->paymentStatus== 'RECEIPTED' || $momoDTO->paymentStatus== 'RECEIPT DELIVERED' ) {  
            if(!$momoDTO->receipt){
               //consider a format receipt public methid for each billing client
               $momoDTO->receipt = "Payment successful\n" .
                  "Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
                  "Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
                  "Acc: " . $momoDTO->accountNumber . "\n";
               $momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
            }
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
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $momoDTO;
   }

}
