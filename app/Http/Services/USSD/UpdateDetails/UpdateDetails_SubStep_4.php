<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\MenuConfigs\CustomerFieldService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_GetCustomerAccount $getCustomerAccount,
      private StepService_ValidateCRMInput $validateCRMInput,
      private CustomerFieldService $customerFieldService,
      private IUpdateDetailsClient $updateDetailsClient)
   {} 

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 4){
         $txDTO->stepProcessed=true;
         try {
            $capturedUpdates = \json_decode(Cache::get($txDTO->sessionId."Updates",''),true);
            $capturedUpdates = $capturedUpdates? $capturedUpdates:[];
            $order = \count($capturedUpdates) + 1;
            $customerFields = $this->customerFieldService->findAll([
                                          'client_id' => $txDTO->client_id
                                       ]);
            $customerField = \array_values(\array_filter($customerFields, function ($record) use($order){
                  return ($record->order == $order);
               }));
            $customerField = $customerField[0]; 
            $txDTO->subscriberInput = $this->validateCRMInput->handle($customerField,$txDTO->subscriberInput);
            $capturedUpdates[$order] = $txDTO->subscriberInput;
            if(\count($customerFields) == (\count($capturedUpdates))){
               $txDTO->customer = $this->getCustomerAccount->handle(
                                       $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
               $txDTO->subscriberInput = \implode(";",\array_values($capturedUpdates));
               $customerFieldUpdate = [
                                          'accountNumber' => $txDTO->accountNumber,
                                          'mobileNumber' => $txDTO->mobileNumber,
                                          'client_id' => $txDTO->client_id,
                                          'district' => $txDTO->customer['district'],
                                          'updates' => $capturedUpdates
                                    ];
               $caseNumber = $this->updateDetailsClient->create($customerFieldUpdate);
               $txDTO->response = "Application to update customer details successfully submitted. Case number: ".
                                    $caseNumber; 
               $this->sendSMSNotification($txDTO);
               $txDTO->lastResponse = true;
               $txDTO->status='COMPLETED';

            }else{
               Cache::put($txDTO->sessionId."Updates",\json_encode($capturedUpdates), 
                              Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
               $customerField = \array_values(\array_filter($customerFields, function ($record)use($order){
                     return ($record->order == $order +1);
                  }));
               $customerField = $customerField[0]; 
               $txDTO->response = $customerField->prompt;
               $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
               $txDTO->subscriberInput = \end($arrCustomerJourney);
               \array_pop($arrCustomerJourney);
               $txDTO->customerJourney =\implode("*", $arrCustomerJourney);
            }

         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At update details step 4. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

   private function sendSMSNotification(BaseDTO $txDTO): void
   {
      $arrSMSes = [
               [
                  'mobileNumber' => $txDTO->mobileNumber,
                  'client_id' => $txDTO->client_id,
                  'message' => $txDTO->response,
                  'type' => 'NOTIFICATION',
               ]
         ];
      Queue::later(Carbon::now()->addSeconds(3), 
                     new SendSMSesJob($arrSMSes));
   }

}