<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\MenuConfigs\CustomerFieldService;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_Step_3
{

   public function __construct(
      private CustomerFieldService $customerFieldService)
   {}

   public function run(BaseDTO $txDTO)
   {
      
      try {
         if($txDTO->subscriberInput != '1'){
            throw new Exception("Invalid input", 1);
         }
         $customerFields = $this->customerFieldService->findAll([
                                 'client_id' => $txDTO->client_id
                              ]);
         $customerField = \array_values(\array_filter($customerFields, function ($record){
                     return ($record->order == 1);
                  }));
         $customerField = $customerField[0];  
         $txDTO->response = $customerField->prompt;
      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At update details step 3. '.$e->getMessage();
      }
      return $txDTO;

   }

}