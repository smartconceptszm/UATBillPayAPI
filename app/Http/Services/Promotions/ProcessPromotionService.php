<?php

namespace App\Http\Services\Promotions;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ProcessPromotionService
{

   public function handle(BaseDTO $promotionDTO)
   {
      

      //Process the request
      try {
         
         $promotionDTO  =  App::make(Pipeline::class)
                              ->send($promotionDTO)
                              ->through(
                                 [
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_CustomerQualification::class,
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_PaymentsQualification::class,
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_CaculateReward::class,
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_SaveEntry::class,
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_EnterRaffle::class,
                                    \App\Http\Services\Promotions\PromotionHandlers\Step_SendMessage::class
                                 ]
                              )
                              ->thenReturn();

      } catch (\Throwable $e) {
         $promotionDTO->error='At promotion pipeline. '.$e->getMessage();
         Log::info($promotionDTO->error);
      }

      return $promotionDTO;
      
   }

}