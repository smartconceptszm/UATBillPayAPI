<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_Step_3
{

   public function __construct( 
      private BillingCredentialService $billingCredentialService,
      private PaymentHistoryService $paymentHistoryService,
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ClientMenuService $clientMenuService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         if($txDTO->subscriberInput == '1'){
            $paymentsProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
				if($paymentsProviderStatus['enabled']){
               $selectedMenu = $this->clientMenuService->findOneBy([
                                    'client_id' => $txDTO->client_id,
                                    'isPayment' => "YES",
                                    'isDefault' => "YES",
                                    'isActive' => "YES",
                                    'billingClient' =>  $clientMenu->billingClient
                                 ]);
               if($selectedMenu->handler != 'Parent'){
                  $txDTO->customerJourney = $arrCustomerJourney[0]."*".$selectedMenu->order."*".$arrCustomerJourney[2];
                  $txDTO->subscriberInput = $txDTO->mobileNumber;
                  $txDTO->handler = $selectedMenu->handler;
                  $txDTO->menu_id = $selectedMenu->id;
                  if($clientMenu->amountPrompt){
                     $txDTO->response = $clientMenu->amountPrompt.":\n";
                  }else{
                     $txDTO->response="Enter Amount :\n";
                  }
                  $txDTO->status = USSDStatusEnum::Initiated->value;
               }else{
                  $txDTO->response = " Dial *".$arrCustomerJourney[0]."# and choose the menu for payment!";
                  $txDTO->lastResponse = true;
               }
            }else{
               throw new Exception($paymentsProviderStatus['responseText'], 1);
            }
            return $txDTO;
         }

         if($txDTO->subscriberInput == '0'){
            $txDTO->customerJourney = $arrCustomerJourney[0];
            $txDTO->subscriberInput = $arrCustomerJourney[1];
            $txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
            $txDTO->status = USSDStatusEnum::Initiated->value;
            return $txDTO;
         }

         if($txDTO->subscriberInput == '2'){
            $billingCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
            $payments = $this->paymentHistoryService->findByCustomerAccount([
                              'limit' => $billingCredentials['PAYMENT_HISTORY'],
                              'customerAccount' => $txDTO->customerAccount,
                              'client_id' => $txDTO->client_id,
                           ]);
            if($payments){
               $prompt = "Payment history for ".$txDTO->customerAccount.":\n";
               foreach ($payments as $key=>$payment) {
                  
                  $prompt .= ($key+1).". ".Carbon::parse($payment->created_at)->format('d-M-Y').
                              //" ".$payment->customerAccount.
                              " ZMW ".number_format($payment->receiptAmount, 2, '.', ',')."\n";
               }
               $txDTO->response = $prompt;
               $txDTO->lastResponse = true;
            }else{
               $txDTO->response = "There are no Mobile Money based payments to Acc: ".$txDTO->customerAccount;//." from ".$txDTO->mobileNumber;
               $txDTO->lastResponse = true;
            }
            return $txDTO;
         }

         $txDTO->customerAccount = $arrCustomerJourney[2];
         $txDTO->error = 'Invalid selection';
         $txDTO->errorType = USSDStatusEnum::InvalidInput->value;

      } catch (\Throwable $e) {
         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = USSDStatusEnum::WalletNotActivated->value;
         }else{
            $txDTO->error = 'At check balance step 3. '.$e->getMessage();
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
      }
      return $txDTO;
   }

}