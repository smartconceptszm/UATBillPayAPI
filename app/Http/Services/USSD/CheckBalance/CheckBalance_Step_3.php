<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_Step_3
{

   public function __construct( 
      private PaymentHistoryService $paymentHistoryService,
      private StepService_AccountNoMenu $accountNoMenu,
      private ClientMenuService $clientMenuService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {

         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         
         if($txDTO->subscriberInput == '1'){
            $selectedMenu = $this->clientMenuService->findOneBy([
                                 'client_id' => $txDTO->client_id,
                                 'isPayment' => "YES",
                                 'isDefault' => "YES",
                                 'isActive' => "YES",
                                 'accountType' => $txDTO->accountType
                              ]);
            $selectedMenu = \is_null($selectedMenu)?null:(object)$selectedMenu->toArray();
            if($selectedMenu->handler != 'Parent'){
               $txDTO->customerJourney = $arrCustomerJourney[0]."*".$selectedMenu->order;
               $txDTO->subscriberInput = $arrCustomerJourney[2];
               $txDTO->menu_id = $selectedMenu->id;
               $txDTO->response = "Enter Amount :\n";
               $txDTO->status = 'INITIATED';
            }else{
               $txDTO->response = " Dial *".$arrCustomerJourney[0]."# and choose to pay!";
               $txDTO->lastResponse = true;
            }
            return $txDTO;
         }

         if($txDTO->subscriberInput == '0'){
            $txDTO->customerJourney = $arrCustomerJourney[0];
            $txDTO->subscriberInput = $arrCustomerJourney[1];
            $txDTO->response = $this->accountNoMenu->handle($txDTO->urlPrefix,$txDTO->accountType);
            $txDTO->status = 'INITIATED';
            return $txDTO;
         }

         if($txDTO->subscriberInput == '2'){
            $payments = $this->paymentHistoryService->findAll([
                              'limit' => \env(\strtoupper($txDTO->urlPrefix).'_PAYMENT_HISTORY'),
                              'accountNumber' => $txDTO->accountNumber,
                              //'mobileNumber' => $txDTO->mobileNumber,
                              'client_id' => $txDTO->client_id,
                           ]);
            if($payments){
               $prompt = "Payment history for ".$txDTO->accountNumber.":\n";
               foreach ($payments as $key=>$payment) {
                  
                  $prompt .= ($key+1).". ".Carbon::parse($payment->created_at)->format('d-M-Y').
                              //" ".$payment->accountNumber.
                              " ZMW ".number_format($payment->receiptAmount, 2, '.', ',')."\n";
               }
               $txDTO->response = $prompt;
               $txDTO->lastResponse = true;
            }else{
               $txDTO->response = "There are no Mobile Money based payments to Acc: ".$txDTO->accountNumber;//." from ".$txDTO->mobileNumber;
               $txDTO->lastResponse = true;
            }
            return $txDTO;
         }

         $txDTO->accountNumber = $arrCustomerJourney[2];
         $txDTO->error = 'Invalid selection';
         $txDTO->errorType= "InvalidInput";

      } catch (\Throwable $e) {
         $txDTO->error = 'At check balance step 3. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
   }

}