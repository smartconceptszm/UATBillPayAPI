<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Promotions\PromotionService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\ProcessPromotionJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_ProcessPromotion extends EfectivoPipelineContract
{

   public function __construct(
      private ClientWalletService $clientWalletService,
		private PromotionService $promotionService) 
	{}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if ($this->isTransactionEligible($paymentDTO)) {
               $this->processPromotion($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At process promotion. ' . $e->getMessage();
      }
      return $paymentDTO;

   }

   private function isTransactionEligible($paymentDTO): bool
   {

      $hasActivePromotion = false;
      $eligibleStatuses = [
                              PaymentStatusEnum::Receipted->value,
                              PaymentStatusEnum::Receipt_Delivered->value
                           ];
      if(in_array($paymentDTO->paymentStatus, $eligibleStatuses)){
         $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
         $activePromotion = $this->promotionService->findOneBy([
                                          'client_id'=>$clientWallet->client_id,
                                          'status'=>'ACTIVE']);
         if($activePromotion){
            $hasActivePromotion = true;
         }
   
      }

      return  $hasActivePromotion;

   }

   private function processPromotion(BaseDTO $paymentDTO): void
   {
      
      ProcessPromotionJob::dispatch($paymentDTO)
                                    ->delay(Carbon::now()->addSeconds(1))
                                    ->onQueue('low');
   }

}