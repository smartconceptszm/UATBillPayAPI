<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Payments\ReceiptService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class PostLocalReceipt
{

   public function __construct(
      private ReceiptService $receiptService
   )
   {}

   public function handle(BaseDTO $paymentDTO):array|null
   {

      try {
			$receipt = $this->receiptService->findOneBy([
                                    'client_id'=>$paymentDTO->client_id,
                                    'payment_id'=>$paymentDTO->id
                                 ]);

         if(!$receipt){
                  $receipt = $this->receiptService->create([
                              'description' => $paymentDTO->receipt,
                              'client_id'=>$paymentDTO->client_id,
                              'payment_id'=>$paymentDTO->id
                           ]);
         }
         $paymentDTO->receiptNumber =  $receipt->id;
         $receiptingParams = [
                              "payment_provider" => strtolower($paymentDTO->walletHandler).'_money', 
                              "payer_msisdn"=> $paymentDTO->mobileNumber, 
                              "txnDate"=> Carbon::now()->format('Y-m-d'),
                              "ReceiptNo"=> $paymentDTO->receiptNumber,
                              "account"=> $paymentDTO->customerAccount,
                              "amount" => $paymentDTO->receiptAmount,
                              "txnId"=> $paymentDTO->transactionId,
                              "client_id"=> $paymentDTO->client_id,
                              "transDesc"=>"PrePaid"
                           ];
         return $receiptingParams;
         
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}