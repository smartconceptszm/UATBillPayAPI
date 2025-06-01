<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Promotions\PromotionMenuService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_PaymentsQualification extends EfectivoPipelineContract
{

   public function __construct(
      private PromotionMenuService $promotionMenuService,
      private ClientMenuService $clientMenuService)
   {}

   protected function stepProcess(BaseDTO $promotionDTO)
   {

      try {
         Log::info("(luapula) Step 2 Payment Qualification Account Number".$promotionDTO->customerAccount);
         $promoMenu = $this->promotionMenuService->findOneBy([
                                                      'promotion_id' =>$promotionDTO->promotion_id,
                                                      'menu_id' =>$promotionDTO->menu_id,
                                                ]);
         if($promoMenu){
            $theMenu = $this->clientMenuService->findById($promotionDTO->menu_id);
            $promotionDTO->paymentType = $theMenu->prompt;
            if($promotionDTO->paymentAmount < $promotionDTO->promotionEntryAmount){
               $promotionDTO->error = 'Payment amount does not qualify';
               $promotionDTO->exitPipeline = true;
            }
         }else{
            $promotionDTO->error = 'Payment type does not qualify';
            $promotionDTO->exitPipeline = true;
         }

      } catch (\Throwable $e) {
         $promotionDTO->error = 'At promotion step 2. ' . $e->getMessage();
      }
      return $promotionDTO;

   }

}