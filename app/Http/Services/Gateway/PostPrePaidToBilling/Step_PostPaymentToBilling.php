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
         Log::info('('.$this->paymentDTO->urlPrefix.') Payment posted to Billing System. '.
                        '- Transaction ID = '.$this->paymentDTO->transactionId.               
                        '- Session Id: '.$this->paymentDTO->sessionId.
                        '- Channel: '.$this->paymentDTO->channel.
                        '- Wallet: '.$this->paymentDTO->walletNumber.
                        '- Receipt: '.$this->paymentDTO->receipt);
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment to billing. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}