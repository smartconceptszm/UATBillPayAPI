<?php

namespace App\Http\Services\USSD\CouncilPaymentHistory;

use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentHistory_Step_2 
{

   public function __construct( 
      private BillingCredentialService $billingCredentialService,
      private PaymentHistoryService $paymentHistoryService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         switch ($txDTO->subscriberInput) {
            case '1':
               $billingCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
               $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
               $txDTO->subscriberInput = $txDTO->mobileNumber;
               $payments = $this->paymentHistoryService->findByWallet([
                                 'limit' => $billingCredentials['PAYMENT_HISTORY'],
                                 'mobileNumber' => $txDTO->mobileNumber,
                                 'client_id' => $txDTO->client_id,
                              ]);
               if($payments){
                  $prompt = "Payment history for ".$txDTO->mobileNumber.":\n";
                  foreach ($payments as $key=>$payment) {
                     $prompt .= ($key+1).". ".Carbon::parse($payment->created_at)->format('d-M-Y').                   
                                 " ZMW ".number_format($payment->receiptAmount, 2, '.', ',').
                                 " ".$payment->prompt."\n";
                  }
                  $txDTO->response = $prompt;
               }else{
                  $txDTO->response = "There are no Mobile Money based payments from: ".$txDTO->mobileNumber;
               }
               $txDTO->lastResponse = true;
               break;
            case '2':
               $txDTO->response = "Enter the phone number (e.g 09xx xxxxxx/07xx xxxxxx)\n";
               break;
            default:
               throw new Exception("Invalid selection", 1);
               break;
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = USSDStatusEnum::InvalidInput->value;
         }else{
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
         $txDTO->error = 'Council payment history step 2. '. $e->getMessage();
      }
      return $txDTO;
      
   }
   
}