<?php

namespace App\Http\Services\USSD\CouncilPaymentHistory;

use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentHistory_Step_3 
{

   public function __construct( 
      private BillingCredentialService $billingCredentialService,
      private PaymentHistoryService $paymentHistoryService,
      private ValidateCRMInput $validateInput
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $txDTO->subscriberInput = $this->validateInput->handle('MOBILE',$txDTO->subscriberInput);
         $billingCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
         $payments = $this->paymentHistoryService->findByWallet([
                           'limit' => $billingCredentials['PAYMENT_HISTORY'],
                           'mobileNumber' => $txDTO->subscriberInput,
                           'client_id' => $txDTO->client_id,
                        ]);
         if($payments){
            $prompt = "Payment history for ".$txDTO->subscriberInput.":\n";
            foreach ($payments as $key=>$payment) {
               $prompt .= ($key+1).". ".Carbon::parse($payment->created_at)->format('d-M-Y').                   
                           " ZMW ".number_format($payment->receiptAmount, 2, '.', ',').
                           " ".$payment->prompt."\n";
            }
            $txDTO->response = $prompt;
         }else{
            $txDTO->response = "There are no Mobile Money based payments from: ".$txDTO->subscriberInput;
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = USSDStatusEnum::InvalidInput->value;
         }else{
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
         $txDTO->error = 'Council payment history step 3. '. $e->getMessage();
      }
      return $txDTO;
      
   }
   
}