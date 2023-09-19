<?php

namespace App\Http\Services\Payments;

use App\Http\Services\External\BillingClients\BillingClientBinderService;
use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\MoMo\Utility\Step_LogStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentManaulPostService
{

   public function __construct(
      private BillingClientBinderService $bindBillingClientService,
      private PaymentToReviewService $paymentToReviewService, 
      private MoMoDTO $momoDTO)
   {}

   public function create(array $data):object|null{
      try {
         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         if($momoDTO->receiptNumber != ''){
            throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
         }
         if($momoDTO->mnoTransactionId != ''){
            if($momoDTO->mnoTransactionId != $data['mnoTransactionId']){
               throw new Exception($momoDTO->mnoName ." transaction Id ".$data['mnoTransactionId']. " already captured.");
            }
         }  
         //Bind the Services
            $this->bindBillingClientService->bind($momoDTO->urlPrefix);
         //
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->mnoTransactionId = $data['mnoTransactionId'];
         $momoDTO->error = "";
         $momoDTO = app(Pipeline::class)
                  ->send($momoDTO)
                  ->through(
                     [
                        Step_PostPaymentToClient::class,
                        Step_SendReceiptViaSMS::class,
                        Step_UpdateTransaction::class,  
                        Step_LogStatus::class 
                     ]
                  )
                  ->thenReturn();
         $response = (object)[
                     'data' => $momoDTO->receipt   
               ];
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

}
