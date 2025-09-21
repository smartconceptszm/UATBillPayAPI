<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Promotions\RaffleDrawEntryService;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_EnterRaffle extends EfectivoPipelineContract
{

   public function __construct(
      private RaffleDrawEntryService $promotionDrawEntryService)
   {}

   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {

         if(($promotionDTO->enterPromo) && ($promotionDTO->paymentAmount >= $promotionDTO->promotionRaffleEntryAmount)){
            $this->promotionDrawEntryService->create($promotionDTO->toRaffleEntryData());
         }

      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 1. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}