<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   public function __construct( 
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private StepService_AccountNoMenu $accountNoMenu,
      private ClientMenuService $clientMenuService
   ){}

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         if(\count($arrCustomerJourney) == 4){
            $txDTO->stepProcessed = true;
            if($txDTO->subscriberInput == '1'){
               $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
               if($momoPaymentStatus['enabled']){
                  if($arrCustomerJourney[2] == '2'){
                     $txDTO->menu = "BuyUnits";
                  }else{
                     $txDTO->menu = "PayBill";
                  }
                  $selectedMenu = $this->clientMenuService->findOneBy([
                                       'code' => $txDTO->subscriberInput,
                                       'client_id' => $txDTO->client_id,
                                       'isActive' => "YES"
                                    ]);
                  $txDTO->customerJourney = $arrCustomerJourney[0]."*".$selectedMenu->order;
                  $txDTO->subscriberInput = $arrCustomerJourney[3];
                  $txDTO->response = "Enter Amount :\n";
                  $txDTO->status = 'INITIATED';
               }else{
                  $txDTO->response = $momoPaymentStatus['responseText'];
                  $txDTO->lastResponse = true;
               }
               return $txDTO;


            }
            if($txDTO->subscriberInput == '0'){
               $txDTO->customerJourney = $arrCustomerJourney[0].'*'.$arrCustomerJourney[1];
               $txDTO->subscriberInput = $arrCustomerJourney[2];
               $prePaidText = $txDTO->subscriberInput == "2"? "PRE-PAID ":"";
               $txDTO->response = $this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
               $txDTO->status = 'INITIATED';
               return $txDTO;
            }
   
            $txDTO->accountNumber = $arrCustomerJourney[2];
            $txDTO->error = 'Invalid selection';
            $txDTO->errorType= "InvalidInput";
   
         }
         return $txDTO;
      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'MoMoOffline';
         }else{
            $txDTO->error = 'At check balance step 4. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }

   }

}