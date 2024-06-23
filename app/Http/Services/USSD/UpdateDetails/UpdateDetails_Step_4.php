<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\Web\MenuConfigs\CustomerFieldService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_Step_4
{

   public function __construct(
      private IEnquiryHandler $getCustomerAccount,
      private StepService_ValidateCRMInput $validateCRMInput,
      private CustomerFieldService $customerFieldService,
      private IUpdateDetailsClient $updateDetailsClient)
   {} 

   public function run(BaseDTO $txDTO)
   {

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
         $txDTO->subscriberInput = $this->validateCRMInput->handle($customerField->type,$txDTO->subscriberInput);
         $capturedUpdates[$order] = $txDTO->subscriberInput;
         if(\count($customerFields) == (\count($capturedUpdates))){
            $txDTO = $this->getCustomerAccount->handle($txDTO);
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

      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At update details step 4. '.$e->getMessage();
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
                     new SendSMSesJob($arrSMSes),'','low');
   }

}