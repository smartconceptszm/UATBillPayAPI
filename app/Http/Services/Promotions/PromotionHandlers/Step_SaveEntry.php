<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Promotions\PromotionEntryService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_SaveEntry extends EfectivoPipelineContract
{

   public function __construct(
      private PromotionEntryService $promotionEntryService)
   {}

   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {
         if($promotionDTO->enterPromo){
            $promotionDTO->entryDate = Carbon::now();
            $promoEntry = $this->promotionEntryService->create($promotionDTO->toPromotionEntryData());
            $promotionDTO->id = $promoEntry->id;
         }
      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 4. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}