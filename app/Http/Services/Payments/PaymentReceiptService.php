<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\Gateway\Utility\Step_UpdateTransaction;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Gateway\Utility\Step_LogStatus;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientMnoService;
use App\Jobs\PaymentsAnalyticsRegularJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentReceiptService
{

   public function __construct(
      private BillingCredentialService $billingCredentialService,
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ClientMnoService $clientMnoService,
      private MoMoDTO $paymentDTO)
   {}

   public function create(array $data):object|null{
      
      try {

         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if(!($paymentDTO->paymentStatus == 'PAID | NO TOKEN' || $paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED')){
            throw new Exception("Unexpected payment status - ".$paymentDTO->paymentStatus);
         }

         if($paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED' && $paymentDTO->receiptNumber != ''){
            throw new Exception("Payment appears receipted! Receipt number ".$paymentDTO->receiptNumber);
         }

         if($paymentDTO->paymentStatus == 'PAID | NO TOKEN' && $paymentDTO->tokenNumber != ''){
            throw new Exception("Token was already issued! Token number ".$paymentDTO->tokenNumber);
         }

         if($paymentDTO->ppTransactionId == ''){
            throw new Exception("MNO transaction Id is null. Payment not yet confirmed!");
         }  

         //Bind Receipting Handler
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK'] == "YES"){
               $receiptingHandler = "MockReceipting";
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\External\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //
         
         if($user = Auth::user()){
            $paymentDTO->user_id = $user->id;
         }

         $paymentDTO->error = "";
         $paymentDTO =  App::make(Pipeline::class)
                        ->send($paymentDTO)
                        ->through(
                           [
                              Step_PostPaymentToClient::class,
                              Step_SendReceiptViaSMS::class,
                              Step_UpdateTransaction::class,  
                              Step_LogStatus::class 
                           ]
                        )
                        ->thenReturn();
      
         $theDate = Carbon::parse($paymentDTO->created_at);
         if(!$theDate->isToday()){
            Queue::later(Carbon::now()->addSeconds(1),new PaymentsAnalyticsRegularJob($paymentDTO),'','high');
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO;
      
   }

   public function update(array $data, string $id):object|null{
      try {
         $thePayment = $this->paymentToReviewService->findById($id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));

         if($paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED'){
            if($paymentDTO->receiptNumber != ''){
               throw new Exception("Payment already receipted! Receipt number ".$paymentDTO->receiptNumber);
            }
            $paymentDTO->receiptNumber = $data['receiptNumber'];
            $paymentDTO->paymentStatus = "RECEIPTED";
   
            //consider a format receipt public method for each billing client
               $paymentDTO->receipt = "Payment successful\n" .
                     "Rcpt No.: " . $paymentDTO->receiptNumber . "\n" .
                     "Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
                     "Acc: " . $paymentDTO->customerAccount . "\n";
               $paymentDTO->receipt .= "Date: " . Carbon::now()->format('d-M-Y') . "\n";
            //
         }else{
            if($paymentDTO->tokenNumber != ''){
               throw new Exception("Token already issued! Token number ".$paymentDTO->tokenNumber);
            }
            $paymentDTO->tokenNumber = $data['receiptNumber'];
            $paymentDTO->paymentStatus = "RECEIPTED";
   
            //consider a format receipt public method for each billing client
               $paymentDTO->receipt = "Payment successful\n" .
                     "Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
                     "Acc: " . $paymentDTO->customerAccount . "\n".
                     "Token: ". $paymentDTO->tokenNumber . "\n".
                     "Date: " . Carbon::now()->format('d-M-Y') . "\n";
            //
         }

         $paymentDTO = App::make(Pipeline::class)
                     ->send($paymentDTO)
                     ->through(
                        [
                           Step_SendReceiptViaSMS::class,
                           Step_UpdateTransaction::class,  
                           Step_LogStatus::class 
                        ]
                     )
                     ->thenReturn();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO;
      
   }


}
