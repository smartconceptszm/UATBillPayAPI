<?php

namespace App\Jobs;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ReConfirmPayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\MoMoDTO;
use App\Jobs\BaseJob;

class ReConfirmCallBackPaymentJob extends BaseJob
{

   public $timeout = 180;
   public function __construct(
      private $id)
   {}

   public function handle(PaymentToReviewService $paymentToReviewService,
                                 ReConfirmPayment $reConfirmPayment, MoMoDTO $paymentDTO){

      $thePayment = $paymentToReviewService->findById($this->id);
      $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));
      if($paymentDTO->paymentStatus == PaymentStatusEnum::Submitted->value || 
                     $paymentDTO->paymentStatus == PaymentStatusEnum::Submission_Failed->value ){
         Log::info('('.$paymentDTO->urlPrefix.') CallBack Reconfirmation job launched. Transaction ID = '.$paymentDTO->transactionId.
                     '- Channel: '.$paymentDTO->channel.' - Wallet: '.$paymentDTO->walletNumber);
         return $reConfirmPayment->handle($paymentDTO);
      }

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}