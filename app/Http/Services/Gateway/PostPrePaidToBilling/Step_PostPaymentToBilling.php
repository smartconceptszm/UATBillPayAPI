<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidChambeshi;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\SMS\MessageService;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToBilling extends EfectivoPipelineContract
{

   public function __construct(
      private ReceiptPrePaidChambeshi $receiptPrePaidChambeshi,
      private ClientWalletService $clientWalletService,
      private MessageService $messageService)
   {}    

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         $paymentDTO = $this->receiptPrePaidChambeshi->handle($paymentDTO);
         $wallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
         $theSMS = $this->messageService->findOneBy([
                                             'transaction_id' => $paymentDTO->transactionId,
                                             'customerAccount'=>$paymentDTO->customerAccount,
                                             'mobileNumber'=>$paymentDTO->mobileNumber,
                                             'client_id' => $wallet->client_id
                                          ]);
         if($theSMS->status == 'DELIVERED'){
            $paymentDTO->paymentStatus =  PaymentStatusEnum::Receipt_Delivered->value;
         }

      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment to billing. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}