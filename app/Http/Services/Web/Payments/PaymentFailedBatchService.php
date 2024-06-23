<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Payments\PaymentToReviewService;
use App\Http\Services\Web\Payments\PaymentFailedService;
use App\Http\Services\Web\Clients\ClientWalletService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmPaymentJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedBatchService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private PaymentFailedService $paymentFailedService,
      private ClientWalletService $clientWalletService,
      private MoMoDTO $paymentDTO)
   {}

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         $thePayments = $this->paymentToReviewService->findAll($data);
         foreach ($thePayments as $payment) {
            $thePayment = $this->paymentToReviewService->findById($payment->id);
            $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
            $paymentDTO->user_id = $user->id;
            $paymentDTO->error = "";
            Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                                                   new ReConfirmPaymentJob($paymentDTO),'','high');
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
