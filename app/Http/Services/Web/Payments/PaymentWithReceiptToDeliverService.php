<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\Gateway\Utility\Step_UpdateTransaction;
use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Web\Payments\PaymentToReviewService;
use App\Http\Services\Gateway\Utility\Step_LogStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentWithReceiptToDeliverService
{

   public function __construct(
      private BillingCredentialService $billingCredentialService,
      private PaymentToReviewService $paymentToReviewService,
      private MoMoDTO $paymentDTO)
   {}

   public function update(string $id):string{
      try {
         $thePayment = $this->paymentToReviewService->findById($id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if ($paymentDTO->paymentStatus == 'RECEIPTED' || $paymentDTO->paymentStatus == 'RECEIPT DELIVERED' ) {  
            if(!$paymentDTO->receipt){
               $paymentDTO->receipt = "Payment successful\n" .
                                       "Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n";
               $paymentDTO->receiptNumber ? $paymentDTO->receipt.= "Rcpt No.: ".$paymentDTO->receiptNumber."\n":"";
               $paymentDTO->tokenNumber ? $paymentDTO->receipt.= "Token: ".$paymentDTO->tokenNumber."\n":"";
               $paymentDTO->customerAccount ? $paymentDTO->receipt.= "Acc: " . $paymentDTO->customerAccount . "\n":"";
               $paymentDTO->receipt.="Date: " . Carbon::parse($paymentDTO->updated_at)->format('d-M-Y'). "\n";
            }

            //Bind the SMS Clients
               $billingCredentials = $this->billingCredentialService->getClientCredentials($paymentDTO->client_id);
               $smsClientKey = '';
               if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
                  $smsClientKey = 'MockSMSDelivery';
               }
               if(!$smsClientKey && ($billingCredentials['HAS_OWNSMS'] == 'YES')){
                  $smsClientKey = \strtoupper($paymentDTO->urlPrefix).'SMS';
               }
               if(!$smsClientKey){
                  $smsClientKey = \env('SMPP_CHANNEL');
               }
               App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
            //

            if($user = Auth::user()){
               $paymentDTO->user_id = $user->id;
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
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO->receipt;
   }
}
