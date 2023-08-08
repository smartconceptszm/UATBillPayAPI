<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class ServiceApplications_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $serviceTypeService;
   public function __construct(ServiceTypeService $serviceTypeService)
   {
      $this->serviceTypeService = $serviceTypeService;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 1){
         $txDTO->stepProcessed=true;
         try {
               $utilityServiceTypes = $this->serviceTypeService->findAll(['client_id'=>$txDTO->client_id]);
               $stringMenu = "Select application:\n";
               foreach ($utilityServiceTypes as $utilityService) {
                  $stringMenu.=$utilityService->order.'. '.$utilityService->name."\n";
               }
               $txDTO->response=$stringMenu; 
         } catch (\Throwable $e) {
               $txDTO->error='At Retrieving complaint types. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}