<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_CustomerQualification extends EfectivoPipelineContract
{



   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {
         if(!($promotionDTO->consumerType == $promotionDTO->promotionConsumerType || $promotionDTO->promotionConsumerType == "ALL")){
            $promotionDTO->error = 'Customer does not qualify';
            $promotionDTO->exitPipeline = true;
         }
      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 1. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}