<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Promotions\PromotionRateService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_CaculateReward extends EfectivoPipelineContract
{

   public function __construct(
      private PromotionRateService $promotionRateService)
   {}

   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {
         Log::info("(luapula) Step 3 Payment Qualification Account Number: ".$promotionDTO->customerAccount);
         if($promotionDTO->enterPromo){
            if($promotionDTO->promotionType == 'FLATRATE'){
               $promotionDTO->rewardAmount = $promotionDTO->paymentAmount * ($promotionDTO->promotionRateValue/100);
               $promotionDTO->rewardRate =$promotionDTO->promotionRateValue;
            }else{
               $promotionRates = $this->promotionRateService->findAll(['promotion_id'=>$promotionDTO->promotion_id]);
               foreach ($promotionRates as $promotionRate) {
                  if($promotionDTO->paymentAmount >= $promotionRate->minAmount && $promotionDTO->paymentAmount <= $promotionRate->maxAmount){
                     $promotionDTO->rewardAmount = $promotionDTO->paymentAmount * ($promotionRate->rate/100);
                     $promotionDTO->rewardRate = $promotionRate->rate;
                     break;
                  }
               }
            }

            if($promotionDTO->paymentAmount >= $promotionDTO->promotionRaffleEntryAmount){
               $promotionDTO->message = sprintf($promotionDTO->promotionRaffleEntryMessage,
                                    \number_format((float)$promotionDTO->rewardAmount, 2, '.', ',')
                                 );
               $promotionDTO->raffleEntryMessage = $promotionDTO->message;

            }else{
               $promotionDTO->message = sprintf($promotionDTO->promotionEntryMessage,
                                 \number_format((float)$promotionDTO->rewardAmount, 2, '.', ','),
                                 \number_format((float)$promotionDTO->promotionRaffleEntryAmount, 2, '.', ',')
                              );

            }

         }

      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 3. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}