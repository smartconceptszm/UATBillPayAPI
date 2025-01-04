<?php

namespace App\Jobs;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\MoMoDTO;
use App\Jobs\BaseJob;

class ReConfirmCallBackPaymentJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private $id)
   {}

   public function handle(PaymentToReviewService $paymentToReviewService,ConfirmPayment $confirmPayment, MoMoDTO $paymentDTO)
   {

      $thePayment = $paymentToReviewService->findById($this->id);
      $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));
      if($paymentDTO->paymentStatus == PaymentStatusEnum::Submitted->value){
         Log::info('('.$this->paymentDTO->urlPrefix.') CallBack Reconfirmation job launched. Transaction ID = '.$this->paymentDTO->transactionId.
                     '- Channel: '.$this->paymentDTO->channel.' - Wallet: '.$this->paymentDTO->walletNumber);
         return $confirmPayment->handle($paymentDTO);
      }

   }

}