<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidChambeshi;
use App\Http\Services\Payments\PaymentService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class PostThePrePaidToBilling
{

   public function __construct(
			private ReceiptPrePaidChambeshi $receiptPrePaidChambeshi,
			private PaymentService $paymentService)
   {}

   public function handle(BaseDTO $paymentDTO)
   {
		
		try {
			$this->receiptPrePaidChambeshi->handle($paymentDTO);
			$this->paymentService->update($paymentDTO->toPaymentData(),$paymentDTO->id);
		} catch (\Throwable $e) {
         $paymentDTO->error='At post the pre-paid to chambeshi billing. '.$e->getMessage();
         Log::info($paymentDTO->error);
		}

   }


}