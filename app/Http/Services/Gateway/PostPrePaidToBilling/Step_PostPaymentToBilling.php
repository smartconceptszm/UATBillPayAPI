<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidChambeshi;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToBilling extends EfectivoPipelineContract
{

   public function __construct(
      private ReceiptPrePaidChambeshi $receiptPrePaidChambeshi)
   {}    

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         $paymentDTO = $this->receiptPrePaidChambeshi->handle($paymentDTO);
         Log::info('('.$paymentDTO->urlPrefix.') Payment posted to Billing System. '.
                        '- Transaction ID = '.$paymentDTO->transactionId.               
                        '- Session Id: '.$paymentDTO->sessionId.
                        '- Channel: '.$paymentDTO->channel.
                        '- Wallet: '.$paymentDTO->walletNumber.
                        '- Receipt: '.$paymentDTO->receipt);
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment to billing. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}