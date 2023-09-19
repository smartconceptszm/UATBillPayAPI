<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\ServiceTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ServiceTypeService $serviceTypeService)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 1){
         $txDTO->stepProcessed = true;
         try {
               $utilityServiceTypes = $this->serviceTypeService->findAll(['client_id'=>$txDTO->client_id]);
               $stringMenu = "Select application:\n";
               foreach ($utilityServiceTypes as $utilityService) {
                  $stringMenu.=$utilityService->order.'. '.$utilityService->name."\n";
               }
               $txDTO->response=$stringMenu; 
         } catch (Exception $e) {
               $txDTO->error='At service application step 1. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}