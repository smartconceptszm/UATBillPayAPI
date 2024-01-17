<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Payments\PaymentFailedService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedBatchService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private PaymentFailedService $paymentFailedService,
      private MoMoDTO $momoDTO)
   {}

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         $thePayments = $this->paymentToReviewService->findAll($data);
         foreach ($thePayments as $payment) {
            $thePayment = $this->paymentToReviewService->findById($payment->id);
            $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
            $user = Auth::user(); 
            $momoDTO->user_id = $user->id;
            $momoDTO->error = "";
            Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                                                   new ReConfirmMoMoPaymentJob($momoDTO));
         }
         $response = (object)[
                              'data' => \count($thePayments).' payments submitted for review. Check status after a while'      
                           ];
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;

   }

}
