<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_Step_3
{

   public function __construct( 
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private StepService_AccountNoMenu $accountNoMenu,
      private ClientMenuService $clientMenuService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed = true;
         if($txDTO->subscriberInput == '1'){
            $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
            if($momoPaymentStatus['enabled']){
               $selectedMenu = $this->clientMenuService->findOneBy([
                                    'client_id' => $txDTO->client_id,
                                    'isPayment' => "YES",
                                    'isDefault' => "YES",
                                    'isParent' => "NO",
                                    'isActive' => "YES"
                                 ]);
               $txDTO->customerJourney = $arrCustomerJourney[0]."*".$selectedMenu->order;
               $txDTO->subscriberInput = $arrCustomerJourney[2];
               $txDTO->menu_id = $selectedMenu->id;
               $txDTO->response = "Enter Amount :\n";
               $txDTO->status = 'INITIATED';
            }else{
               $txDTO->response = $momoPaymentStatus['responseText'];
               $txDTO->lastResponse = true;
            }
            return $txDTO;
         }
         if($txDTO->subscriberInput == '0'){
            $txDTO->customerJourney = $arrCustomerJourney[0];
            $txDTO->subscriberInput = $arrCustomerJourney[1];
            $txDTO->response = $this->accountNoMenu->handle($txDTO->urlPrefix);
            $txDTO->status = 'INITIATED';
            return $txDTO;
         }
         $txDTO->accountNumber = $arrCustomerJourney[2];
         $txDTO->error = 'Invalid selection';
         $txDTO->errorType= "InvalidInput";
         return $txDTO;
      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'MoMoOffline';
         }else{
            $txDTO->error = 'At check balance step 3. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }

   }

}