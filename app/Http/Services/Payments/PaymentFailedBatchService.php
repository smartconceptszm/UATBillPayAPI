<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Payments\PaymentFailedService;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentFailedBatchService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private PaymentFailedService $paymentFailedService)
   {}

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         $thePayments = $this->paymentToReviewService->findAll($data);
         foreach ($thePayments as $payment) {
            $this->paymentFailedService->update($data,$payment->id);
         }
         $response = (object)[
                              'data' => \count($thePayments).' payments submitted for review. Check status after a while'      
                           ];
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $response;

   }

}
