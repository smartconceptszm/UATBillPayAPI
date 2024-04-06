<?php

namespace App\Http\Services\Payments;

use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\MoMo\Utility\Step_LogStatus;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentReceiptService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private MoMoDTO $momoDTO)
   {}

   public function create(array $data):object|null{
      
      try {
         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));

         if($momoDTO->accountType == "POST-PAID"){
            if($momoDTO->paymentStatus != 'PAID | NOT RECEIPTED' ){
               throw new Exception("Unexpected payment status - ".$momoDTO->paymentStatus);
            }
            if($momoDTO->receiptNumber != ''){
               throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
            }
         }else{
            if(!($momoDTO->paymentStatus == 'PAID | NO TOKEN' || $momoDTO->paymentStatus == 'PAID | NOT RECEIPTED')){
               throw new Exception("Unexpected payment status - ".$momoDTO->paymentStatus);
            }
            if($momoDTO->tokenNumber != '' && $momoDTO->paymentStatus == 'PAID | NO TOKEN'){
               throw new Exception("Token was already issued! Token number ".$momoDTO->tokenNumber);
            }
            if($momoDTO->receiptNumber != '' &&  $momoDTO->paymentStatus == 'PAID | NOT RECEIPTED'){
               throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
            }
         }

         if($momoDTO->mnoTransactionId == ''){
            throw new Exception("MNO transaction Id is null. Payment not yet confirmed!");
         }  

         //Bind Receipting Handler
            $theMenu = $this->clientMenuService->findById($momoDTO->menu_id);
            $theMenu = \is_null($theMenu)?null: (object)$theMenu->toArray();
            $receiptingHandler = $theMenu->receiptingHandler;
            if (\env('USE_RECEIPTING_MOCK') == "YES"){
               $receiptingHandler = "MockReceipting";
            }
            App::bind(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //

         //Bind the SMS Clients
            $smsClientKey = '';
            if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClientKey = 'MockSMSDelivery';
            }
            if(!$smsClientKey && (\env(\strtoupper($momoDTO->urlPrefix).'_HAS_OWNSMS') == 'YES')){
               $smsClientKey = \strtoupper($momoDTO->urlPrefix).'SMS';
            }
            if(!$smsClientKey){
               $smsClientKey = \env('SMPP_CHANNEL');
            }
            App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
         //
         $user = Auth::user(); 
         if($user){
            $momoDTO->user_id = $user->id;
         }
         $momoDTO->error = "";
         $momoDTO =  App::make(Pipeline::class)
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
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $momoDTO;
      
   }

   public function update(array $data, string $id):object|null{
      try {
         $thePayment = $this->paymentToReviewService->findById($id);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));

         if($momoDTO->accountType == "POST-PAID"){
            if($momoDTO->receiptNumber != ''){
               throw new Exception("Payment already receipted! Receipt number ".$momoDTO->receiptNumber);
            }
            $momoDTO->receiptNumber = $data['receiptNumber'];
            $momoDTO->paymentStatus = "RECEIPTED";
   
            //consider a format receipt public method for each billing client
               $momoDTO->receipt = "Payment successful\n" .
                     "Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
                     "Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
                     "Acc: " . $momoDTO->accountNumber . "\n";
               $momoDTO->receipt .= "Date: " . Carbon::now()->format('d-M-Y') . "\n";
            //
         }else{
            if($momoDTO->tokenNumber != ''){
               throw new Exception("Token already issued! Token number ".$momoDTO->tokenNumber);
            }
            $momoDTO->tokenNumber = $data['receiptNumber'];
            $momoDTO->paymentStatus = "RECEIPTED";
   
            //consider a format receipt public method for each billing client
               $momoDTO->receipt = "Payment successful\n" .
                     "Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
                     "Meter No: " . $momoDTO->meterNumber . "\n" .
                     "Acc: " . $momoDTO->accountNumber . "\n".
                     "Token: ". $momoDTO->tokenNumber . "\n".
                     "Date: " . Carbon::now()->format('d-M-Y') . "\n";
            //
         }

         //Bind the SMS Clients
            $smsClientKey = '';
            if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClientKey = 'MockSMSDelivery';
            }
            if(!$smsClientKey && (\env(\strtoupper($momoDTO->urlPrefix).'_HAS_OWNSMS') == 'YES')){
               $smsClientKey = \strtoupper($momoDTO->urlPrefix).'SMS';
            }
            if(!$smsClientKey){
               $smsClientKey = \env('SMPP_CHANNEL');
            }
            App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
         //

         $momoDTO = App::make(Pipeline::class)
                     ->send($momoDTO)
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
      return $momoDTO;
      
   }


}
