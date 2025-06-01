<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Promotions\PromotionEntryService;
use App\Http\Services\SMS\SMSService;
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;

class Step_SendMessage extends EfectivoPipelineContract
{
   public function __construct(
      private PromotionEntryService $promotionEntryService,
      private SMSService $smsService,
      private SMSTxDTO $smsTxDTO
   ) {}

   protected function stepProcess(BaseDTO $promotionDTO)
   {
      try {
         if($promotionDTO->enterPromo){
            $smsDTO = $this->smsTxDTO->fromArray($promotionDTO->toSMSData());
            $smsDTO = $this->smsService->send($smsDTO);
            if($smsDTO->status == "DELIVERED"){
               $promotionDTO->smsDelivered = "YES";
               $this->promotionEntryService->update($promotionDTO->toPromotionEntryData(),$promotionDTO->id);
            }
         }
      } catch (\Throwable $e) {
         $promotionDTO->error = 'At receipt via SMS. ' . $e->getMessage();
      }

      return $promotionDTO;
   }

}