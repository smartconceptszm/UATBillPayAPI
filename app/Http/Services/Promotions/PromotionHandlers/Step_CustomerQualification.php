<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_CustomerQualification extends EfectivoPipelineContract
{



   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {
         Log::info("(luapula) Step 1 Customer Qualification Account Number".$promotionDTO->customerAccount);
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         if($billpaySettings['PROMOTION_MOCK_'.\strtoupper($promotionDTO->urlPrefix)] == "YES"){
            $testMSISDN = \explode("*", $billpaySettings['APP_ADMIN_MSISDN']);
            $testMSISDN = array_filter($testMSISDN,function($entry){
                                                      return $entry !== "";
                                                   });
            if ((\in_array($promotionDTO->mobileNumber, $testMSISDN))) {
               if(!($promotionDTO->consumerType == $promotionDTO->promotionConsumerType || $promotionDTO->promotionConsumerType == "ALL")){
                  $promotionDTO->error = 'Customer does not qualify';
                  $promotionDTO->exitPipeline = true;
               }
            }else{
               $promotionDTO->error = 'Customer does not qualify';
               $promotionDTO->exitPipeline = true;
            }
         }else{
            if(!($promotionDTO->consumerType == $promotionDTO->promotionConsumerType || $promotionDTO->promotionConsumerType == "ALL")){
               $promotionDTO->error = 'Customer does not qualify';
               $promotionDTO->exitPipeline = true;
            }
         }
      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 1. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}