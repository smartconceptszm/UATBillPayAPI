<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\CustomerFieldService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class UpdateDetails_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $customerFieldService;
   public function __construct(CustomerFieldService $customerFieldService)
   {
      $this->customerFieldService = $customerFieldService;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if(\count(\explode("*", $txDTO->customerJourney)) == 3){
         $txDTO->stepProcessed = true;
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
         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At update details step 3. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

}