<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Payments\PaymentFailedService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ConfirmPaymentJob;
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
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            ConfirmPaymentJob::dispatch($paymentDTO)
                                    ->delay(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY']))
                                    ->onQueue('low');
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